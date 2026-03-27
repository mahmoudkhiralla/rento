<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $query = Notification::where('user_id', $user->id)->latest('id');

        if ($request->boolean('unread_only', false)) {
            $query->whereNull('read_at');
        }
        if ($request->filled('channel')) {
            $query->where('channel', $request->string('channel'));
        }
        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        $perPage = (int) $request->integer('per_page', 10);
        $perPage = max(1, min($perPage, 50));

        $notifications = $query->paginate($perPage)->withQueryString();

        return response()->json([
            'success' => true,
            'data' => $notifications,
        ]);
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
}
