<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PropertyResource;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\Amenity;
use App\Models\PropertyType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Property::with(['type', 'images']);
        if (Schema::hasColumn('properties', 'status')) {
            $query->where('status', 'published');
        } else {
            $query->where('approved', true);
        }

        // Filter by landlord (owner) id
        if ($request->filled('landlord_id')) {
            $query->where('user_id', $request->landlord_id);
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }
        if ($request->filled('type_id')) {
            $query->where('property_type_id', $request->type_id);
        }
        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
        }

        return PropertyResource::collection($query->paginate(12));
    }

    /**
     * Explicit endpoint to list published properties (alias to index).
     */
    public function published(Request $request)
    {
        return $this->index($request);
    }

    /**
     * List properties for the authenticated landlord.
     */
    public function mine(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        // Allow landlord or accounts with both roles
        $isAdmin = (method_exists($user, 'hasRole') && $user->hasRole('admin'));
        $isLandlordRole = (method_exists($user, 'hasRole') && $user->hasRole('landlord'));
        $isLandlordType = in_array(($user->user_type ?? null), ['landlord', 'both'], true);
        if (! ($isAdmin || $isLandlordRole || $isLandlordType)) {
            return response()->json(['message' => 'يتطلب صلاحيات مؤجر'], 403);
        }

        $query = Property::with(['type', 'images'])
            ->where('user_id', $user->id)
            ->latest('id');

        // Optional status filter: published | unpublished | inprogress
        $status = $request->string('status')->toString();
        if ($status !== '') {
            if (Schema::hasColumn('properties', 'status')) {
                if (in_array($status, ['published', 'unpublished', 'inprogress'], true)) {
                    $query->where('status', $status);
                }
            } else {
                if ($status === 'published') {
                    $query->where('approved', true);
                } elseif ($status === 'unpublished') {
                    $query->where('approved', false);
                } elseif ($status === 'inprogress') {
                    $query->whereNull('approved');
                }
            }
        }

        // Optional filters
        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }
        if ($request->filled('type_id')) {
            $query->where('property_type_id', $request->type_id);
        }

        return PropertyResource::collection($query->paginate(12)->withQueryString());
    }

    /**
     * قائمة مختصرة لعقارات المؤجر الحالي: id و title فقط.
     */
    public function mineSummary(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        $isAdmin = (method_exists($user, 'hasRole') && $user->hasRole('admin'));
        $isLandlordRole = (method_exists($user, 'hasRole') && $user->hasRole('landlord'));
        $isLandlordType = in_array(($user->user_type ?? null), ['landlord', 'both'], true);
        if (! ($isAdmin || $isLandlordRole || $isLandlordType)) {
            return response()->json(['message' => 'يتطلب صلاحيات مؤجر'], 403);
        }

        $props = Property::select('id', 'title')
            ->where('user_id', $user->id)
            ->orderBy('title')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $props,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        // السماح فقط للمؤجر (أو الحسابات المصرح لها لكلا الدورين) بإنشاء طلب نشر عقار
        if (! in_array($user->user_type, ['landlord', 'both'])) {
            return response()->json(['message' => 'فقط المؤجر يمكنه إنشاء طلب نشر عقار'], 403);
        }

        // Validate input fields
        $data = $request->validate([
            'property_type_id' => ['nullable', 'exists:property_types,id'],
            'title' => ['required', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'rental_type' => ['nullable', 'string', 'max:255'], // e.g. يومي | شهري
            'capacity' => ['nullable', 'integer', 'min:1'],
            'bedrooms' => ['nullable', 'integer', 'min:0'],
            'bathrooms' => ['nullable', 'integer', 'min:0'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            // كلمات مفتاحية (يمكن إرسالها كـ array أو كنص مفصول بفواصل)
            'keywords' => ['nullable'],
            'keywords.*' => ['string', 'max:50'],
            // صورة أساسية كـ رابط (حقل قديم image مدعوم أيضًا)
            'primary_image' => ['nullable', 'string'],
            'image' => ['nullable', 'string'],
            'amenity_ids' => ['nullable', 'array'],
            'amenity_ids.*' => ['integer', 'exists:amenities,id'],
            // صور متعددة كروابط
            'images' => ['nullable', 'array'],
            'images.*' => ['string'],
            // صور متعددة كملفات مرفوعة عبر multipart/form-data
            'images_files' => ['nullable', 'array'],
            'images_files.*' => ['file', 'image', 'max:5120'],
            // صورة أساسية كملف منفصل
            'primary_image_file' => ['nullable', 'file', 'image', 'max:5120'],
        ]);

        // Normalize keywords: accept array or comma/semicolon-separated string
        $keywordsInput = $request->input('keywords', []);
        $normalizedKeywords = [];
        if (is_string($keywordsInput)) {
            $parts = preg_split('/[,;\n]+/', $keywordsInput);
            foreach ($parts as $part) {
                $trimmed = trim($part);
                if ($trimmed !== '') {
                    $normalizedKeywords[] = $trimmed;
                }
            }
        } elseif (is_array($keywordsInput)) {
            foreach ($keywordsInput as $kw) {
                if (! is_string($kw)) {
                    continue;
                }
                $trimmed = trim($kw);
                if ($trimmed !== '') {
                    $normalizedKeywords[] = $trimmed;
                }
            }
        }
        // Deduplicate and limit to reasonable count
        $normalizedKeywords = array_values(array_unique($normalizedKeywords));
        if (count($normalizedKeywords) > 20) {
            $normalizedKeywords = array_slice($normalizedKeywords, 0, 20);
        }

        // تحديد الصورة الأساسية (أولوية: primary_image ثم image)
        $primaryImageUrl = $data['primary_image'] ?? $data['image'] ?? null;

        // Create property with default approved=false and status=inprogress when column exists
        $create = [
            'user_id' => $user?->id,
            'property_type_id' => $data['property_type_id'] ?? null,
            'title' => $data['title'],
            'city' => $data['city'] ?? null,
            'address' => $data['address'] ?? null,
            'rental_type' => $data['rental_type'] ?? null,
            'capacity' => $data['capacity'] ?? null,
            'bedrooms' => $data['bedrooms'] ?? null,
            'bathrooms' => $data['bathrooms'] ?? null,
            'price' => $data['price'],
            'description' => $data['description'] ?? null,
            'keywords' => $normalizedKeywords,
            'image' => $primaryImageUrl,
            'approved' => false,
        ];
        if (Schema::hasColumn('properties', 'status')) {
            $create['status'] = 'inprogress';
        }
        $property = Property::create($create);

        // Sync amenities if provided
        $amenityIds = $data['amenity_ids'] ?? [];
        if (! empty($amenityIds)) {
            $property->amenities()->sync($amenityIds);
        }

        // Handle multiple images from URLs
        $imageUrls = $data['images'] ?? [];
        // تجنب تكرار الصورة الأساسية داخل المعرض إن كانت موجودة ضمن الصور
        $filteredImageUrls = [];
        foreach ($imageUrls as $url) {
            if (! is_string($url) || trim($url) === '') {
                continue;
            }
            if ($primaryImageUrl && trim($url) === trim($primaryImageUrl)) {
                continue;
            }
            $filteredImageUrls[] = $url;
        }
        foreach ($filteredImageUrls as $idx => $url) {
            PropertyImage::create([
                'property_id' => $property->id,
                'url' => $url,
                'sort_order' => $idx,
            ]);
        }

        // Handle uploaded image files (multipart)
        $uploadedFiles = $request->file('images_files', []);
        if (! empty($uploadedFiles)) {
            $uploadsDir = public_path('images/properties');
            if (! is_dir($uploadsDir)) {
                @mkdir($uploadsDir, 0775, true);
            }

            foreach ($uploadedFiles as $idx => $file) {
                if (! $file) {
                    continue;
                }
                $ext = $file->getClientOriginalExtension();
                $filename = (string) Str::uuid().($ext ? ('.'.$ext) : '');
                $file->move($uploadsDir, $filename);
                $publicUrl = asset('images/properties/'.$filename);

                PropertyImage::create([
                    'property_id' => $property->id,
                    'url' => $publicUrl,
                    'sort_order' => $idx + count($filteredImageUrls),
                ]);
            }
        }

        // صورة أساسية كملف منفصل (لا تُضاف للمعرض افتراضيًا)
        if ($request->file('primary_image_file')) {
            $file = $request->file('primary_image_file');
            $uploadsDir = public_path('images/properties');
            if (! is_dir($uploadsDir)) {
                @mkdir($uploadsDir, 0775, true);
            }
            $ext = $file->getClientOriginalExtension();
            $filename = (string) Str::uuid().($ext ? ('.'.$ext) : '');
            $file->move($uploadsDir, $filename);
            $publicUrl = asset('images/properties/'.$filename);
            $property->image = $publicUrl;
            $property->save();
        }

        // If primary image is not set, default to first provided image
        if (! $property->image) {
            $firstImage = PropertyImage::where('property_id', $property->id)
                ->orderBy('sort_order')
                ->value('url');
            if ($firstImage) {
                $property->image = $firstImage;
                $property->save();
            }
        }

        return response()->json([
            'message' => 'تم إنشاء العقار بنجاح (بانتظار الموافقة)',
            'property' => new PropertyResource($property->load(['type', 'amenities', 'images'])),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $property = Property::with(['type', 'amenities', 'images'])->findOrFail($id);

        return new PropertyResource($property);
    }

    public function types(Request $request)
    {
        $query = PropertyType::select('id', 'name', 'rental_type', 'is_active');
        if ($request->filled('is_active')) {
            $val = $request->string('is_active')->toString();
            if ($val !== 'all') {
                $query->where('is_active', filter_var($val, FILTER_VALIDATE_BOOLEAN));
            }
        } else {
            $query->where('is_active', true);
        }
        if ($request->filled('rental_type')) {
            $query->where('rental_type', $request->rental_type);
        }
        if ($request->filled('q')) {
            $q = trim((string) $request->q);
            if ($q !== '') {
                $query->where('name', 'like', "%{$q}%");
            }
        }
        return response()->json($query->orderBy('name')->get());
    }

    public function amenities(Request $request)
    {
        $query = Amenity::select('id', 'name');
        if ($request->filled('q')) {
            $q = trim((string) $request->q);
            if ($q !== '') {
                $query->where('name', 'like', "%{$q}%");
            }
        }
        return response()->json($query->orderBy('name')->get());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $isAdmin = (method_exists($user, 'hasRole') && $user->hasRole('admin'));
        $isLandlordRole = (method_exists($user, 'hasRole') && $user->hasRole('landlord'));
        $isLandlordType = in_array(($user->user_type ?? null), ['landlord', 'both'], true);
        if (! ($isAdmin || $isLandlordRole || $isLandlordType)) {
            return response()->json(['message' => 'يتطلب صلاحيات مؤجر'], 403);
        }

        $property = Property::with(['amenities', 'images'])->find($id);
        if (! $property) {
            return response()->json(['message' => 'العقار غير موجود'], 404);
        }
        if (! $isAdmin && $property->user_id !== $user->id) {
            return response()->json(['message' => 'غير مصرح بتعديل هذا العقار'], 403);
        }

        // Validate input (partial updates allowed)
        $data = $request->validate([
            'property_type_id' => ['sometimes', 'nullable', 'exists:property_types,id'],
            'title' => ['sometimes', 'string', 'max:255'],
            'city' => ['sometimes', 'nullable', 'string', 'max:255'],
            'address' => ['sometimes', 'nullable', 'string', 'max:255'],
            'rental_type' => ['sometimes', 'nullable', 'string', 'max:255'],
            'capacity' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'bedrooms' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'bathrooms' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'description' => ['sometimes', 'nullable', 'string'],
            'keywords' => ['sometimes', 'nullable'],
            'keywords.*' => ['string', 'max:50'],
            'amenity_ids' => ['sometimes', 'array'],
            'amenity_ids.*' => ['integer', 'exists:amenities,id'],
            'primary_image' => ['sometimes', 'nullable', 'string'],
            'image' => ['sometimes', 'nullable', 'string'],
            'images' => ['sometimes', 'array'],
            'images.*' => ['string'],
            // ملفات
            'primary_image_file' => ['sometimes', 'nullable', 'file', 'image', 'max:5120'],
            'images_files' => ['sometimes', 'array'],
            'images_files.*' => ['file', 'image', 'max:5120'],
        ]);

        // Normalize keywords if provided
        if ($request->has('keywords')) {
            $keywordsInput = $request->input('keywords', []);
            $normalizedKeywords = [];
            if (is_string($keywordsInput)) {
                $parts = preg_split('/[,;\n]+/', $keywordsInput);
                foreach ($parts as $part) {
                    $trimmed = trim($part);
                    if ($trimmed !== '') {
                        $normalizedKeywords[] = $trimmed;
                    }
                }
            } elseif (is_array($keywordsInput)) {
                foreach ($keywordsInput as $kw) {
                    if (! is_string($kw)) { continue; }
                    $trimmed = trim($kw);
                    if ($trimmed !== '') { $normalizedKeywords[] = $trimmed; }
                }
            }
            $normalizedKeywords = array_values(array_unique($normalizedKeywords));
            if (count($normalizedKeywords) > 20) { $normalizedKeywords = array_slice($normalizedKeywords, 0, 20); }
            $property->keywords = $normalizedKeywords;
        }

        // Map simple fields
        foreach ([
            'property_type_id','title','city','address','rental_type','capacity','bedrooms','bathrooms','price','description'
        ] as $field) {
            if ($request->has($field)) {
                $property->{$field} = $data[$field] ?? null;
            }
        }

        // Update status/approved when applicable
        if (Schema::hasColumn('properties', 'status')) {
            if ($request->has('status')) {
                $allowed = ['published', 'unpublished', 'inprogress'];
                $status = (string) $request->input('status');
                if (in_array($status, $allowed, true)) {
                    $property->status = $status;
                }
            }
        } else {
            if ($request->has('approved')) {
                $property->approved = filter_var($request->input('approved'), FILTER_VALIDATE_BOOLEAN);
            }
        }

        // Primary image (URL)
        if ($request->has('primary_image') || $request->has('image')) {
            $primaryImageUrl = $data['primary_image'] ?? $data['image'] ?? null;
            $property->image = $primaryImageUrl;
        }

        // Primary image (uploaded file)
        if ($request->file('primary_image_file')) {
            $file = $request->file('primary_image_file');
            $uploadsDir = public_path('images/properties');
            if (! is_dir($uploadsDir)) { @mkdir($uploadsDir, 0775, true); }
            $ext = $file->getClientOriginalExtension();
            $filename = (string) Str::uuid().($ext ? ('.'.$ext) : '');
            $file->move($uploadsDir, $filename);
            $publicUrl = asset('images/properties/'.$filename);
            $property->image = $publicUrl;
        }

        $property->save();

        // Sync amenities if provided
        if (array_key_exists('amenity_ids', $data)) {
            $amenityIds = $data['amenity_ids'] ?? [];
            $property->amenities()->sync($amenityIds);
        }

        // Replace gallery images if provided via URLs and/or uploaded files
        $hasImagesUrls = array_key_exists('images', $data);
        $hasImagesFiles = $request->hasFile('images_files');
        if ($hasImagesUrls || $hasImagesFiles) {
            // Build union of gallery URLs
            $newGallery = [];
            $imageUrls = ($hasImagesUrls ? ($data['images'] ?? []) : []);
            foreach ($imageUrls as $url) {
                if (! is_string($url) || trim($url) === '') { continue; }
                $newGallery[] = trim($url);
            }
            // Process uploaded files
            $uploadedFiles = $request->file('images_files', []);
            if (! empty($uploadedFiles)) {
                $uploadsDir = public_path('images/properties');
                if (! is_dir($uploadsDir)) { @mkdir($uploadsDir, 0775, true); }
                foreach ($uploadedFiles as $file) {
                    if (! $file) { continue; }
                    $ext = $file->getClientOriginalExtension();
                    $filename = (string) Str::uuid().($ext ? ('.'.$ext) : '');
                    $file->move($uploadsDir, $filename);
                    $publicUrl = asset('images/properties/'.$filename);
                    $newGallery[] = $publicUrl;
                }
            }

            // Deduplicate and avoid duplicating primary image in gallery
            $newGallery = array_values(array_unique(array_map('trim', $newGallery)));
            if ($property->image) {
                $newGallery = array_values(array_filter($newGallery, function ($url) use ($property) {
                    return trim($url) !== trim((string) $property->image);
                }));
            }

            // Replace existing gallery
            PropertyImage::where('property_id', $property->id)->delete();
            foreach ($newGallery as $idx => $url) {
                PropertyImage::create([
                    'property_id' => $property->id,
                    'url' => $url,
                    'sort_order' => $idx,
                ]);
            }
        }

        return response()->json([
            'message' => 'تم تحديث العقار بنجاح',
            'property' => new PropertyResource($property->fresh(['type','amenities','images'])),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        $isAdmin = (method_exists($user, 'hasRole') && $user->hasRole('admin'));
        $isLandlordRole = (method_exists($user, 'hasRole') && $user->hasRole('landlord'));
        $isLandlordType = in_array(($user->user_type ?? null), ['landlord', 'both'], true);
        if (! ($isAdmin || $isLandlordRole || $isLandlordType)) {
            return response()->json(['message' => 'يتطلب صلاحيات مؤجر'], 403);
        }

        $property = Property::find($id);
        if (! $property) {
            return response()->json(['message' => 'العقار غير موجود'], 404);
        }
        if (! $isAdmin && $property->user_id !== $user->id) {
            return response()->json(['message' => 'غير مصرح بحذف هذا العقار'], 403);
        }

        // Delete related images and detach amenities
        PropertyImage::where('property_id', $property->id)->delete();
        $property->amenities()->detach();

        $property->delete();

        return response()->json(['success' => true]);
    }
}
