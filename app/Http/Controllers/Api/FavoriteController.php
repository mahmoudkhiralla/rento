<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PropertyResource;
use App\Models\Favorite;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        if (! in_array($user->user_type, ['tenant', 'both'], true)) {
            return response()->json(['message' => 'هذا المسار متاح للمستأجر فقط'], 403);
        }

        $perPage = (int) $request->integer('per_page', 12);
        $query = $user->favoriteProperties()->latest('favorites.created_at');

        return PropertyResource::collection($query->paginate($perPage)->withQueryString());
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        if (! in_array($user->user_type, ['tenant', 'both'], true)) {
            return response()->json(['message' => 'هذا المسار متاح للمستأجر فقط'], 403);
        }

        $data = $request->validate([
            'property_id' => ['required', 'exists:properties,id'],
        ]);

        $property = Property::find($data['property_id']);
        if (! $property) {
            return response()->json(['message' => 'العقار غير موجود'], 404);
        }

        $favorite = Favorite::firstOrCreate([
            'user_id' => $user->id,
            'property_id' => (int) $data['property_id'],
        ]);

        $statusCode = $favorite->wasRecentlyCreated ? 201 : 200;

        return response()->json([
            'message' => $favorite->wasRecentlyCreated ? 'تمت إضافة العقار إلى المفضلة' : 'العقار موجود مسبقًا في المفضلة',
            'favorite' => [
                'id' => $favorite->id,
                'user_id' => $favorite->user_id,
                'property_id' => $favorite->property_id,
            ],
        ], $statusCode);
    }
}

