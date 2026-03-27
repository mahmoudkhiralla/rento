<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SuspendedUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    /**
     * Display a listing of users
     */
    public function list(Request $request)
    {
        // Get statistics
        $totalUsers = User::count();
        $newUsersToday = User::whereDate('created_at', today())->count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $lastMonthUsers = User::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
        $newUsersGrowth = $lastMonthUsers > 0 ?
            round((($newUsersThisMonth - $lastMonthUsers) / $lastMonthUsers) * 100) : 0;

        $activeLandlords = User::where('user_type', 'landlord')
            ->where('status', 'active')
            ->count();
        $landlordsGrowth = 12; // Calculate based on your logic

        // Build query
        $query = User::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by verification status
        if ($request->filter == 'id_verified') {
            $query->where('id_verified', true);
        }
        if ($request->filter == 'face_verified') {
            $query->where('face_verified', true);
        }
        if ($request->filter == 'influencer') {
            $query->where('is_influencer', true);
        }
        if ($request->filter == 'renewal') {
            $query->where('needs_renewal', true);
        }

        // Filter by user type
        if ($request->filled('user_type')) {
            $query->where('user_type', $request->user_type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Additional filters from modal
        if ($request->filled('id_verified')) {
            $query->where('id_verified', true);
        }
        if ($request->filled('face_verified')) {
            $query->where('face_verified', true);
        }

        // Get paginated results
        $users = $query->latest()->paginate(15)->withQueryString();

        return view('dashboard.users.users-list', compact(
            'users',
            'totalUsers',
            'newUsersToday',
            'newUsersThisMonth',
            'newUsersGrowth',
            'activeLandlords',
            'landlordsGrowth'
        ));
    }

    /**
     * Display user profile (tenant)
     */
    public function profile($id)
    {
        $user = User::with(['reviews', 'previousBookings'])->findOrFail($id);

        return view('dashboard.users.user-profile', compact('user'));
    }

    /**
     * Display landlord profile
     */
    public function landlordProfile($id)
    {
        $user = User::with(['properties', 'reviews'])->findOrFail($id);

        return view('dashboard.users.landlord-profile', compact('user'));
    }

    /**
     * Suspend user (admin action)
     */
    public function suspend(Request $request, User $user)
    {
        $data = $request->validate([
            'duration' => 'nullable|string|in:week,two_weeks,month,review,permanent',
            'reason' => 'nullable|string|max:255',
        ]);

        // Calculate ends_at based on duration
        $endsAt = null;
        switch ($data['duration'] ?? null) {
            case 'week':
                $endsAt = Carbon::now()->addWeek();
                break;
            case 'two_weeks':
                $endsAt = Carbon::now()->addWeeks(2);
                break;
            case 'month':
                $endsAt = Carbon::now()->addMonth();
                break;
            case 'review':
                $endsAt = null; // pending review
                break;
            case 'permanent':
                $endsAt = null; // permanent suspension
                break;
        }

        // Update user status
        $user->status = 'suspended';
        $user->save();

        // Log to suspended_users table
        SuspendedUser::create([
            'user_id' => $user->id,
            'admin_id' => auth('admin')->id(),
            'reason' => $data['reason'] ?? null,
            'duration' => $data['duration'] ?? null,
            'ends_at' => $endsAt,
            'status' => 'suspended',
        ]);

        return back()->with('status', 'تم تعليق حساب المستخدم بنجاح');
    }

    /**
     * Activate user (remove suspension)
     */
    public function activate(Request $request, User $user)
    {
        // Update user status
        $user->status = 'active';
        $user->save();

        // Mark last suspension as released if exists
        $lastSuspension = SuspendedUser::where('user_id', $user->id)
            ->where('status', 'suspended')
            ->latest('id')
            ->first();

        if ($lastSuspension) {
            $lastSuspension->update([
                'status' => 'released',
                'released_at' => Carbon::now(),
            ]);
        }

        return back()->with('status', 'تم تفعيل حساب المستخدم بنجاح');
    }

    /**
     * Activate multiple users (bulk)
     */
    public function activateBulk(Request $request)
    {
        $data = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'integer|exists:users,id',
        ]);

        $ids = collect($data['user_ids'])->unique()->values();
        if ($ids->isEmpty()) {
            return back()->with('status', 'لم يتم تحديد أي مستخدمين');
        }

        // Activate users in bulk
        User::whereIn('id', $ids)->update(['status' => 'active']);

        // Mark last suspension as released for each
        $now = Carbon::now();
        foreach ($ids as $id) {
            $lastSuspension = SuspendedUser::where('user_id', $id)
                ->where('status', 'suspended')
                ->latest('id')
                ->first();
            if ($lastSuspension) {
                $lastSuspension->update([
                    'status' => 'released',
                    'released_at' => $now,
                ]);
            }
        }

        return back()->with('status', 'تم تفعيل الحسابات المحددة بنجاح');
    }
}
