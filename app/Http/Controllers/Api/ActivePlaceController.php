<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ActivePlaceResource;
use App\Models\ActivePlace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivePlaceController extends Controller
{
    /**
     * قائمة الأماكن النشطة مع عوامل تصفية اختيارية.
     */
    public function index(Request $request)
    {
        $query = ActivePlace::query();

        if ($request->filled('user_type')) {
            $query->where('user_type', $request->string('user_type'));
        }

        if ($request->filled('city')) {
            $query->where('city', $request->string('city'));
        }

        // عامل اختياري لإظهار المنشور فقط
        if ($request->boolean('published_only', false)) {
            $query->where('is_published', true);
        }

        return ActivePlaceResource::collection($query->paginate(12));
    }

    /**
     * تفاصيل مكان نشط محدد.
     */
    public function show(string $id)
    {
        $place = ActivePlace::findOrFail($id);

        return new ActivePlaceResource($place);
    }

    /**
     * إنشاء مكان نشط جديد للمؤجر (يتطلب تسجيل دخول).
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if (! in_array($user->user_type, ['landlord', 'both'])) {
            return response()->json(['message' => 'فقط المؤجر يمكنه إضافة مكان نشط'], 403);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'area' => ['nullable', 'string', 'max:255'],
            'available_from' => ['nullable', 'date'],
            'booking_type' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'price_unit' => ['required', 'string', 'max:255'],
            'rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'image' => ['nullable', 'string'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $place = ActivePlace::create([
            'user_id' => $user->id,
            'user_type' => 'landlord',
            'name' => $data['name'],
            'city' => $data['city'],
            'area' => $data['area'] ?? null,
            'available_from' => $data['available_from'] ?? null,
            'booking_type' => $data['booking_type'] ?? null,
            'price' => $data['price'],
            'price_unit' => $data['price_unit'],
            'rating' => $data['rating'] ?? null,
            'image' => $data['image'] ?? null,
            'is_published' => $data['is_published'] ?? true,
        ]);

        return response()->json([
            'message' => 'تم إنشاء المكان النشط بنجاح',
            'place' => new ActivePlaceResource($place),
        ], 201);
    }
}
