<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ExternalUsersController extends Controller
{
    /**
     * List users created/updated, filterable by type and dates.
     */
    public function index(Request $request)
    {
        $type = $request->query('type'); // landlord | tenant
        $createdSince = $request->query('created_since');
        $updatedSince = $request->query('updated_since');
        $perPage = (int) $request->query('per_page', 20);
        $perPage = max(1, min($perPage, 100));

        $query = User::query()
            ->when($type, function ($q, $t) {
                $t = strtolower($t);
                if (in_array($t, ['landlord', 'tenant'])) {
                    $q->where('user_type', $t);
                }
            })
            ->when($createdSince, function ($q, $d) {
                $q->where('created_at', '>=', $d);
            })
            ->when($updatedSince, function ($q, $d) {
                $q->where('updated_at', '>=', $d);
            })
            ->latest('id');

        $users = $query->paginate($perPage);

        return UserResource::collection($users);
    }

    /**
     * Show single user with basic relations.
     */
    public function show(User $user)
    {
        $user->load(['wallet', 'properties', 'bookings', 'reviews']);

        return new UserResource($user);
    }

    /**
     * Create a user coming from the main site registration.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:landlord,tenant',
            'name' => 'required|string|max:255',
            'email' => 'required|email:rfc,dns|unique:users,email',
            'phone' => 'nullable|string|max:50|unique:users,phone',
            'avatar' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'job' => 'nullable|string|max:255',
            'has_pet' => 'nullable|boolean',
            'status' => 'nullable|string|max:50',
            'id_verified' => 'nullable|boolean',
            'face_verified' => 'nullable|boolean',
            'password' => 'nullable|string|min:6',
        ]);

        $password = $data['password'] ?? Str::random(12);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($password),
            'phone' => $data['phone'] ?? null,
            'avatar' => $data['avatar'] ?? null,
            'user_type' => $data['type'],
            'id_verified' => (bool) ($data['id_verified'] ?? false),
            'face_verified' => (bool) ($data['face_verified'] ?? false),
            'is_influencer' => false,
            'needs_renewal' => false,
            'status' => $data['status'] ?? 'active',
            'job' => $data['job'] ?? null,
            'city' => $data['city'] ?? null,
            'has_pet' => (bool) ($data['has_pet'] ?? false),
        ]);

        // Ensure wallet exists for the new user
        Wallet::firstOrCreate(['user_id' => $user->id], [
            'balance' => 0,
            // 'points_balance' may exist depending on schema; omitted to be safe
        ]);

        return response()->json([
            'message' => 'User created',
            'user' => new UserResource($user->fresh()),
        ], 201);
    }

    /**
     * Update a user record if main site propagates changes.
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'type' => 'nullable|in:landlord,tenant',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email:rfc,dns|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:50',
            'avatar' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'job' => 'nullable|string|max:255',
            'has_pet' => 'nullable|boolean',
            'status' => 'nullable|string|max:50',
            'id_verified' => 'nullable|boolean',
            'face_verified' => 'nullable|boolean',
            'password' => 'nullable|string|min:6',
        ]);

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        if (isset($data['type'])) {
            $data['user_type'] = $data['type'];
            unset($data['type']);
        }

        $user->update($data);

        // Ensure wallet exists
        Wallet::firstOrCreate(['user_id' => $user->id], [
            'balance' => 0,
        ]);

        return new UserResource($user->fresh());
    }
}
