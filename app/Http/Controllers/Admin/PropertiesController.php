<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Property;
use App\Models\PropertyType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class PropertiesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Dashboard properties page: provide dynamic metrics and list without changing UI structure.
     */
    public function dashboardIndex(Request $request)
    {
        $hasStatus = Schema::hasColumn('properties', 'status');
        // Metrics
        $totalProperties = Property::count();
        $activeProperties = $hasStatus ? Property::where('status', 'published')->count() : Property::where('approved', true)->count();
        $recentAdded = Property::where('created_at', '>=', now()->subDays(7))->count();
        $recentApproved = ($hasStatus
            ? Property::where('status', 'published')
            : Property::where('approved', true))
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        // List (paginate 10) with relations for landlord and type
        $query = Property::with(['user', 'type'])
            ->orderByDesc('created_at');

        // Optional filter via query param: filter=all|active|inactive|inprogress|recent
        $activeFilter = $request->string('filter')->toString();
        if ($activeFilter === 'recent') {
            // الأحدث خلال 7 أيام
            $query->where('created_at', '>=', now()->subDays(7));
        }
        if ($hasStatus) {
            if ($activeFilter === 'active') {
                $query->where('status', 'published');
            } elseif ($activeFilter === 'inactive') {
                $query->where('status', 'unpublished');
            } elseif ($activeFilter === 'inprogress') {
                $query->where('status', 'inprogress');
            } elseif ($activeFilter !== 'all') {
                $query->whereIn('status', ['published', 'unpublished']);
            }
        } else {
            if ($activeFilter === 'active') {
                $query->where('approved', true);
            } elseif ($activeFilter === 'inactive') {
                $query->where('approved', false);
            } elseif ($activeFilter === 'inprogress') {
                $query->whereNull('approved');
            } elseif ($activeFilter !== 'all') {
                $query->where('approved', true);
            }
        }

        // Basic search by title, city, address or landlord
        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($qq) use ($q) {
                $qq->where('title', 'like', "%{$q}%")
                    ->orWhere('city', 'like', "%{$q}%")
                    ->orWhere('address', 'like', "%{$q}%")
                    ->orWhereHas('user', function ($uq) use ($q) {
                        $uq->where('name', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%");
                    });
            });
        }
        // Multi filters: rental_types[], property_type_ids[], cities[]
        $rentalTypesIn = array_filter((array) $request->input('rental_types', []));
        if (! empty($rentalTypesIn)) {
            $query->whereIn('rental_type', $rentalTypesIn);
        }
        // Support single rental_type when multi not provided
        if (empty($rentalTypesIn) && $request->filled('rental_type')) {
            $query->where('rental_type', $request->input('rental_type'));
        }

        $typeIdsIn = array_filter((array) $request->input('property_type_ids', []));
        if (! empty($typeIdsIn)) {
            $query->whereIn('property_type_id', $typeIdsIn);
        }

        $citiesIn = array_filter((array) $request->input('cities', []));
        if (! empty($citiesIn)) {
            $query->whereIn('city', $citiesIn);
        }

        // Backward-compatible single filters when multi not provided
        if (empty($citiesIn) && $request->filled('city')) {
            $query->where('city', $request->city);
        }
        if (empty($typeIdsIn) && $request->filled('property_type_id')) {
            $query->where('property_type_id', $request->property_type_id);
        }
        if ($request->filled('price_min')) {
            $query->where('price', '>=', (float) $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', (float) $request->price_max);
        }

        $properties = $query->paginate(10)->withQueryString();

        // Options for filters: اعرض الأنواع النشطة فقط في باقي الصفحات
        $propertyTypes = PropertyType::select('id', 'name')
            ->where(function ($q) {
                if (Schema::hasColumn('property_types', 'is_active')) {
                    $q->where('is_active', true);
                }
            })
            ->orderBy('name')
            ->get();
        $citiesList = Property::select('city')->whereNotNull('city')->distinct()->pluck('city');
        $rentalTypes = ['يومي', 'شهري'];

        return view('dashboard.properties.index', compact(
            'properties',
            'totalProperties',
            'activeProperties',
            'recentAdded',
            'recentApproved',
            'activeFilter',
            'propertyTypes',
            'citiesList',
            'rentalTypes'
        ));
    }

    /**
     * صفحة المناطق والتصنيفات: عرض أنواع العقارات مجمعة حسب نوع الإيجار
     * مع إظهار الموقوفة أيضًا داخل هذه الصفحة فقط.
     */
    public function areas(Request $request)
    {
        // اجلب كل الأنواع مع الحقول اللازمة وعداد الإعلانات المنشورة (المعتمدة)
        $allTypes = PropertyType::select('id', 'name', 'rental_type', 'is_active')
            ->when(Schema::hasColumn('property_types', 'rental_type'), function ($q) {
                // لا شرط هنا، سنقسم لاحقًا
            })
            ->orderBy('name')
            ->get();

        // تقسيم إلى يومي/شهري بناءً على العمود إن وُجد، وإلا اجعل الجميع شهري
        $monthlyRentalTypes = collect();
        $dailyRentalTypes = collect();
        if (Schema::hasColumn('property_types', 'rental_type')) {
            $monthlyRentalTypes = $allTypes->filter(fn ($t) => ($t->rental_type ?? 'شهري') === 'شهري');
            $dailyRentalTypes = $allTypes->filter(fn ($t) => ($t->rental_type ?? 'شهري') === 'يومي');
        } else {
            $monthlyRentalTypes = $allTypes;
            $dailyRentalTypes = collect();
        }

        // احسب عدد الإعلانات المعتمدة لكل نوع
        $adsCounts = Property::selectRaw('property_type_id, COUNT(*) as c')
            ->when($hasStatus, function ($q) { $q->where('status', 'published'); }, function ($q) { $q->where('approved', true); })
            ->groupBy('property_type_id')
            ->pluck('c', 'property_type_id');

        // مرّر القوائم إلى الواجهة بشكل كائنات مع حقول إضافية
        $monthlyRentalTypes = $monthlyRentalTypes->map(function ($t) use ($adsCounts) {
            $t->ads_count = (int) ($adsCounts[$t->id] ?? 0);
            $t->is_active = Schema::hasColumn('property_types', 'is_active') ? (bool) ($t->is_active ?? true) : true;

            return $t;
        });
        $dailyRentalTypes = $dailyRentalTypes->map(function ($t) use ($adsCounts) {
            $t->ads_count = (int) ($adsCounts[$t->id] ?? 0);
            $t->is_active = Schema::hasColumn('property_types', 'is_active') ? (bool) ($t->is_active ?? true) : true;

            return $t;
        });

        return view('dashboard.properties.areas', compact('monthlyRentalTypes', 'dailyRentalTypes'));
    }

    /**
     * إنشاء نوع عقار جديد مرتبط بنوع إيجار محدد
     */
    public function storeType(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'rental_type' => ['required', Rule::in(['يومي', 'شهري'])],
        ]);

        $type = PropertyType::create([
            'name' => trim($data['name']),
            'rental_type' => $data['rental_type'],
            'is_active' => true,
        ]);

        return response()->json([
            'id' => $type->id,
            'name' => $type->name,
            'rental_type' => $type->rental_type,
            'is_active' => (bool) $type->is_active,
            'ads_count' => 0,
        ]);
    }

    /**
     * تعديل اسم نوع العقار (AJAX)
     */
    public function updateType(Request $request, PropertyType $type)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:100'],
        ]);

        $type->name = trim($data['name']);
        $type->save();

        return response()->json([
            'id' => $type->id,
            'name' => $type->name,
        ]);
    }

    /**
     * تبديل حالة نوع واحد: إيقاف/تفعيل
     */
    public function toggleType(Request $request, PropertyType $type)
    {
        $activeParam = $request->input('active');
        if ($activeParam === null) {
            $type->is_active = ! (bool) ($type->is_active ?? true);
        } else {
            $type->is_active = filter_var($activeParam, FILTER_VALIDATE_BOOL);
        }
        $type->save();

        return response()->json([
            'id' => $type->id,
            'is_active' => (bool) $type->is_active,
        ]);
    }

    /**
     * إيقاف/تفعيل كل الأنواع تحت نوع إيجار محدد
     */
    public function toggleByRental(Request $request)
    {
        $data = $request->validate([
            'rental_type' => ['required', Rule::in(['يومي', 'شهري'])],
            'active' => ['required'],
        ]);
        $newState = filter_var($data['active'], FILTER_VALIDATE_BOOL);

        PropertyType::where('rental_type', $data['rental_type'])
            ->update(['is_active' => $newState]);

        return response()->json(['ok' => true]);
    }

    /**
     * Dashboard: Property publish requests (pending/unapproved) list.
     */
    public function requests(Request $request)
    {
        $hasStatus = Schema::hasColumn('properties', 'status');
        $query = Property::with(['user', 'type'])
            ->when($hasStatus, function ($q) { $q->where('status', 'inprogress'); }, function ($q) { $q->whereNull('approved'); })
            // اعرض طلبات المؤجرين فقط
            ->whereHas('user', function ($uq) {
                $uq->where('user_type', 'landlord');
            })
            ->orderByDesc('created_at');

        // Basic search by title, city, or landlord name/email
        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($qq) use ($q) {
                $qq->where('title', 'like', "%{$q}%")
                    ->orWhere('city', 'like', "%{$q}%")
                    ->orWhereHas('user', function ($uq) use ($q) {
                        $uq->where('name', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%");
                    });
            });
        }

        // Optional filters
        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }
        // Filter by related PropertyType name if provided
        if ($request->filled('property_type_name')) {
            $typeName = trim($request->property_type_name);
            $query->whereHas('type', function ($tq) use ($typeName) {
                $tq->where('name', 'like', $typeName);
            });
        }
        if ($request->filled('property_type_id')) {
            $query->where('property_type_id', $request->property_type_id);
        }
        if ($request->filled('price_min')) {
            $query->where('price', '>=', (float) $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('price', '<=', (float) $request->price_max);
        }

        $properties = $query->paginate(10)->withQueryString();
        $pendingCount = $hasStatus ? Property::where('status', 'inprogress')->count() : Property::whereNull('approved')->count();

        return view('dashboard.properties.requests', compact('properties', 'pendingCount'));
    }

    /**
     * Preview a single property with dynamic data for the dashboard.
     */
    public function preview(Property $property)
    {
        $hasStatus = Schema::hasColumn('properties', 'status');
        $property->load(['user', 'type', 'amenities', 'images']);
        $landlord = $property->user; // قد يكون null
        $type = $property->type;     // قد يكون null

        // بيانات إضافية للواجهة
        $previewData = [
            'published' => ($hasStatus ? ($property->status === 'published') : (bool) ($property->approved ?? false)),
            'created_at' => $property->created_at,
            'rating' => $landlord->rating ?? null,
            'reviews_count' => $landlord->reviews_count ?? null,
            'id_verified' => (bool) ($landlord->id_verified ?? false),
            'face_verified' => (bool) ($landlord->face_verified ?? false),
        ];

        // أسباب إيقاف النشر (تعريف قياسي)
        $deactivationReasons = [
            'انتهاك السياسات',
            'طلب المالك',
            'معلومات خاطئة',
            'مخالفة الشروط',
            'إعادة التقييم',
            'أخرى',
        ];

        // تجهيز صور المعرض ديناميكياً من قاعدة البيانات مع استبعاد الصورة الأساسية
        $galleryImages = [];
        $primary = trim((string) ($property->image ?? ''));
        if ($property->relationLoaded('images') && $property->images) {
            foreach ($property->images->sortBy('sort_order') as $img) {
                $url = trim((string) ($img->url ?? ''));
                if ($url === '') {
                    continue;
                }
                if ($primary !== '' && $url === $primary) {
                    continue;
                }
                $galleryImages[] = $url;
            }
        }
        // في حال عدم وجود صور، نستخدم تكرار للصورة الأساسية كبديل بصري
        if (empty($galleryImages) && $primary !== '') {
            $galleryImages = array_fill(0, 6, $primary);
        }

        return view('dashboard.properties.preview', [
            'property' => $property,
            'landlord' => $landlord,
            'type' => $type,
            'previewData' => $previewData,
            'galleryImages' => $galleryImages,
            'deactivationReasons' => $deactivationReasons,
        ]);
    }

    /**
     * Preview a publishing request in a dedicated page (different layout).
     */
    public function requestPreview(Property $property)
    {
        $hasStatus = Schema::hasColumn('properties', 'status');
        $property->load(['user', 'type', 'amenities', 'images']);
        $landlord = $property->user;
        $type = $property->type;

        $previewData = [
            'published' => ($hasStatus ? ($property->status === 'published') : (bool) ($property->approved ?? false)),
            'created_at' => $property->created_at,
            'id_verified' => (bool) ($landlord->id_verified ?? false),
            'face_verified' => (bool) ($landlord->face_verified ?? false),
        ];

        $galleryImages = [];
        if (! empty($property->image)) {
            $galleryImages = [
                $property->image,
                $property->image,
                $property->image,
            ];
        }

        return view('dashboard.properties.request_preview', [
            'property' => $property,
            'landlord' => $landlord,
            'type' => $type,
            'previewData' => $previewData,
            'galleryImages' => $galleryImages,
        ]);
    }

    /**
     * Deactivate publishing for a property (set approved = false)
     */
    public function deactivate(Request $request, Property $property)
    {
        $hasStatus = \Illuminate\Support\Facades\Schema::hasColumn('properties', 'status');
        $allowed = [
            'انتهاك السياسات',
            'طلب المالك',
            'معلومات خاطئة',
            'مخالفة الشروط',
            'إعادة التقييم',
            'أخرى',
        ];

        $data = $request->validate([
            'reason' => ['required', Rule::in($allowed)],
            'other_reason' => ['nullable', 'string', 'min:3', 'required_if:reason,أخرى'],
        ]);

        $property->approved = false;
        if ($hasStatus) { $property->status = 'unpublished'; }
        $property->deactivation_reason = ($data['reason'] === 'أخرى' && ! empty($data['other_reason']))
            ? trim($data['other_reason'])
            : $data['reason'];
        $property->deactivated_at = now();
        $property->deactivated_by = auth('admin')->id();
        $property->save();

        // إرسال إشعار للمستخدم (صاحب الإعلان) بذكر السبب
        $clarifiedReason = $property->deactivation_reason;
        if ($property->user) {
            Notification::create([
                'user_id' => $property->user->id,
                'title' => 'تم إيقاف نشر إعلانك',
                'message' => "تم إيقاف نشر إعلان \"{$property->title}\". السبب: {$clarifiedReason}.",
                'type' => 'property_deactivated',
                'meta' => [
                    'property_id' => $property->id,
                    'reason' => $clarifiedReason,
                    'deactivated_by' => auth('admin')->id(),
                ],
            ]);
        }

        return redirect()->route('dashboard.properties.preview', $property)
            ->with('status', 'تم إيقاف نشر الإعلان بنجاح');
    }

    /**
     * Approve a property for publishing.
     */
    public function approve(Request $request, Property $property)
    {
        $hasStatus = \Illuminate\Support\Facades\Schema::hasColumn('properties', 'status');
        $property->approved = true;
        if ($hasStatus) { $property->status = 'published'; }
        if (property_exists($property, 'deactivation_reason')) { $property->deactivation_reason = null; }
        if (property_exists($property, 'deactivated_at')) { $property->deactivated_at = null; }
        if (property_exists($property, 'deactivated_by')) { $property->deactivated_by = null; }
        $property->save();

        if ($property->user) {
            Notification::create([
                'user_id' => $property->user->id,
                'title' => 'تمت الموافقة على إعلانك',
                'message' => "تمت الموافقة على نشر إعلان \"{$property->title}\".",
                'type' => 'property_approved',
                'meta' => [
                    'property_id' => $property->id,
                    'approved_by' => auth('admin')->id(),
                ],
            ]);
        }

        return redirect()->route('dashboard.properties.preview', $property)
            ->with('status', 'تمت الموافقة على طلب النشر بنجاح');
    }

    /**
     * Reject a property publishing request.
     */
    public function reject(Request $request, Property $property)
    {
        $reason = trim($request->input('reason', 'تم رفض طلب النشر'));

        // حذف ملفات الصور المحلية المرتبطة إن وجدت
        $property->load('images');
        foreach ($property->images as $img) {
            $url = $img->url;
            if (is_string($url) && str_contains($url, '/images/properties/')) {
                $relative = str_replace(asset(''), '', $url); // محاولة استخراج المسار النسبي
                $pos = strpos($url, '/images/properties/');
                $path = $pos !== false ? substr($url, $pos) : null;
                if ($path) {
                    $full = public_path($path);
                    if (is_file($full)) {
                        @unlink($full);
                    }
                }
            }
        }

        // حذف العقار (سيُحذف الارتباطات عبر القيود المرجعية)
        $propertyId = $property->id;
        $propertyTitle = $property->title;
        $property->delete();

        if ($property->user) {
            Notification::create([
                'user_id' => $property->user->id,
                'title' => 'تم رفض طلب نشر إعلانك',
                'message' => "تم رفض طلب نشر إعلان \"{$propertyTitle}\" وتم حذف الإعلان.",
                'type' => 'property_rejected',
                'meta' => [
                    'property_id' => $propertyId,
                    'rejected_by' => auth('admin')->id(),
                    'reason' => $reason,
                ],
            ]);
        }

        return redirect()->route('dashboard.properties.requests')
            ->with('status', 'تم رفض طلب النشر وحذف الإعلان بنجاح');
    }

    /**
     * Cleanup: delete old pending/unapproved publish requests older than N days.
     * Default: 30 days. Pass ?older_than_days=NN to override.
     */
    public function cleanupOldRequests(Request $request)
    {
        $hasStatus = Schema::hasColumn('properties', 'status');
        $days = (int) $request->input('older_than_days', 30);
        if ($days < 1) {
            $days = 1;
        }
        $cutoff = now()->subDays($days);

        $properties = Property::with('images')
            ->when($hasStatus, function ($q) { $q->where('status', 'inprogress'); }, function ($q) { $q->whereNull('approved'); })
            ->where('created_at', '<', $cutoff)
            ->get();

        $deletedCount = 0;
        foreach ($properties as $property) {
            // حذف الصور الأساسية إن كانت محلية
            $primary = $property->image;
            if (is_string($primary) && str_contains($primary, '/images/properties/')) {
                $pos = strpos($primary, '/images/properties/');
                $path = $pos !== false ? substr($primary, $pos) : null;
                if ($path) {
                    $full = public_path($path);
                    if (is_file($full)) {
                        @unlink($full);
                    }
                }
            }

            // حذف ملفات الصور المحلية المرتبطة
            foreach ($property->images as $img) {
                $url = $img->url;
                if (is_string($url) && str_contains($url, '/images/properties/')) {
                    $pos = strpos($url, '/images/properties/');
                    $path = $pos !== false ? substr($url, $pos) : null;
                    if ($path) {
                        $full = public_path($path);
                        if (is_file($full)) {
                            @unlink($full);
                        }
                    }
                }
            }

            // حذف العقار نفسه
            $property->delete();
            $deletedCount++;
        }

        return redirect()->route('dashboard.properties.requests')
            ->with('status', "تم حذف {$deletedCount} من طلبات النشر الأقدم من {$days} يومًا");
    }

    /**
     * حذف كل طلبات النشر غير المعتمدة (pending/unapproved) فورًا.
     * يقوم أيضًا بإزالة الصور المحلية المرتبطة بها إن وُجدت.
     */
    public function purgeRequests(Request $request)
    {
        $hasStatus = Schema::hasColumn('properties', 'status');
        $properties = Property::with('images')
            ->when($hasStatus, function ($q) { $q->where('status', 'inprogress'); }, function ($q) { $q->whereNull('approved'); })
            ->get();

        $deletedCount = 0;
        foreach ($properties as $property) {
            // حذف الصورة الأساسية إن كانت محلية
            $primary = $property->image;
            if (is_string($primary) && str_contains($primary, '/images/properties/')) {
                $pos = strpos($primary, '/images/properties/');
                $path = $pos !== false ? substr($primary, $pos) : null;
                if ($path) {
                    $full = public_path($path);
                    if (is_file($full)) {
                        @unlink($full);
                    }
                }
            }

            // حذف ملفات الصور المحلية المرتبطة
            foreach ($property->images as $img) {
                $url = $img->url;
                if (is_string($url) && str_contains($url, '/images/properties/')) {
                    $pos = strpos($url, '/images/properties/');
                    $path = $pos !== false ? substr($url, $pos) : null;
                    if ($path) {
                        $full = public_path($path);
                        if (is_file($full)) {
                            @unlink($full);
                        }
                    }
                }
            }

            // حذف العقار نفسه
            $property->delete();
            $deletedCount++;
        }

        return redirect()->route('dashboard.properties.requests')
            ->with('status', "تم حذف جميع طلبات النشر غير المعتمدة ({$deletedCount})");
    }
}
