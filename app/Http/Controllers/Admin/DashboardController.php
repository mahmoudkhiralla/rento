<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Property;
use App\Models\User;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index()
    {
        $activeLandlords = Role::where('name', 'landlord')->where('guard_name', 'web')->exists()
            ? User::role('landlord')->count()
            : 0;

        $stats = [
            'total_users' => User::count(),
            'active_landlords' => $activeLandlords,
            'total_properties' => Property::count(),
            'total_bookings' => Booking::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
