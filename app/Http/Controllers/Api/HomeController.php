<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Property;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'active_landlords' => User::role('landlord')->count(),
            'total_properties' => Property::count(),
            'total_bookings' => Booking::count(),
        ];

        $featured = Property::latest()->take(6)->get();
        $cities = Property::select('city')->distinct()->take(10)->pluck('city');

        return response()->json([
            'stats' => $stats,
            'featured_properties' => \App\Http\Resources\PropertyResource::collection($featured),
            'main_cities' => $cities,
        ]);
    }
}
