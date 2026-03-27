<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Property;
use App\Models\Transaction;
use App\Models\User;
use App\Models\PaymentCard;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardPageController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'quarter');
        $city = $request->get('city');

        $stats = $this->getStats();
        $summary = $this->getSummary();

        $userGrowthData = $this->getUserGrowthData($period);
        $bookingAreaData = $this->getBookingAreaData($city, $period);
        $financialSummary = $this->getFinancialSummary($period);

        return view('dashboard.index', compact(
            'stats',
            'summary',
            'userGrowthData',
            'bookingAreaData',
            'financialSummary',
            'period'
        ));
    }

    private function getStats()
    {
        $activeLandlords = User::whereIn('user_type', ['landlord', 'both'])->where('status', 'active')->count();

        return [
            'total_users' => User::count(),
            'active_landlords' => $activeLandlords,
            'total_properties' => Property::count(),
            'total_bookings' => Booking::count(),
        ];
    }

    private function getSummary()
    {
        $confirmedBookings = Booking::where('status', 'confirmed')->with('property')->get();
        $bookingValue = $confirmedBookings->sum(function ($b) {
            try {
                $start = Carbon::parse($b->start_date);
                $end = Carbon::parse($b->end_date);
                $days = max(1, $start->diffInDays($end));
            } catch (\Throwable $e) {
                $days = 1;
            }
            $price = (float) ($b->property->price ?? 0);
            return $price * $days;
        });

        $totalRevenue = (float) Transaction::sum('amount');
        $totalCards = PaymentCard::count();
        $usedCards = PaymentCard::whereNotNull('user_id')->orWhere('status', 'active')->count();

        return [
            'booking_value' => (int) round($bookingValue),
            'total_revenue' => (int) round($totalRevenue),
            'total_cards' => (int) $totalCards,
            'used_cards' => (int) $usedCards,
        ];
    }

    private function getUserGrowthData($period)
    {
        $period = match ($period) {
            'month', 'monthly' => 'month',
            'quarter', 'quarterly' => 'quarter',
            'half', 'semiannual' => 'half',
            default => 'quarter',
        };

        if ($period === 'month') {
            $labels = [];
            $tenants = [];
            $landlords = [];
            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $labels[] = $date->locale('ar')->translatedFormat('F');
                $start = $date->copy()->startOfMonth();
                $end = $date->copy()->endOfMonth();
                $tenants[] = User::where('user_type', 'tenant')->whereBetween('created_at', [$start, $end])->count();
                $landlords[] = User::whereIn('user_type', ['landlord', 'both'])->whereBetween('created_at', [$start, $end])->count();
            }
            return compact('labels', 'tenants', 'landlords');
        }

        if ($period === 'half') {
            $labels = ['النصف الأول', 'النصف الثاني'];
            $year = Carbon::now()->year;
            $tenants = [
                User::where('user_type', 'tenant')->whereYear('created_at', $year)->whereMonth('created_at', '>=', 1)->whereMonth('created_at', '<=', 6)->count(),
                User::where('user_type', 'tenant')->whereYear('created_at', $year)->whereMonth('created_at', '>=', 7)->whereMonth('created_at', '<=', 12)->count(),
            ];
            $landlords = [
                User::whereIn('user_type', ['landlord', 'both'])->whereYear('created_at', $year)->whereMonth('created_at', '>=', 1)->whereMonth('created_at', '<=', 6)->count(),
                User::whereIn('user_type', ['landlord', 'both'])->whereYear('created_at', $year)->whereMonth('created_at', '>=', 7)->whereMonth('created_at', '<=', 12)->count(),
            ];
            return compact('labels', 'tenants', 'landlords');
        }

        $labels = ['الربع الأول', 'الربع الثاني', 'الربع الثالث', 'الربع الرابع'];
        $year = Carbon::now()->year;
        $tenants = [];
        $landlords = [];
        for ($q = 1; $q <= 4; $q++) {
            $tenants[] = User::where('user_type', 'tenant')->whereYear('created_at', $year)->whereRaw('QUARTER(created_at) = ?', [$q])->count();
            $landlords[] = User::whereIn('user_type', ['landlord', 'both'])->whereYear('created_at', $year)->whereRaw('QUARTER(created_at) = ?', [$q])->count();
        }
        return compact('labels', 'tenants', 'landlords');
    }

    private function getBookingAreaData($city = null, $period = null)
    {
        [$start, $end] = $this->getPeriodBounds($period);
        $rows = DB::table('bookings as b')
            ->leftJoin('properties as p', 'p.id', '=', 'b.property_id')
            ->selectRaw('COALESCE(p.city, "غير محدد") as city, COUNT(b.id) as total')
            ->whereIn('b.status', ['pending', 'confirmed'])
            ->when($start && $end, function ($qq) use ($start, $end) { $qq->whereBetween('b.created_at', [$start, $end]); })
            ->when($city, function ($qq) use ($city) { $qq->where('p.city', $city); })
            ->groupBy('city')
            ->orderByDesc('total')
            ->limit(4)
            ->get();

        return [
            'labels' => $rows->pluck('city')->toArray(),
            'counts' => $rows->pluck('total')->map(fn($v) => (int) $v)->toArray(),
        ];
    }

    private function getFinancialSummary($period = 'quarter')
    {
        $period = match ($period) {
            'month', 'monthly' => 'month',
            'quarter', 'quarterly' => 'quarter',
            'year', 'annual' => 'year',
            default => 'quarter',
        };

        if ($period === 'month') {
            $labels = [];
            $credits = [];
            $debits = [];
            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $labels[] = $date->locale('ar')->translatedFormat('F');
                $start = $date->copy()->startOfMonth();
                $end = $date->copy()->endOfMonth();
                $credits[] = (float) Transaction::whereBetween('created_at', [$start, $end])->where('amount', '>', 0)->sum('amount');
                $debits[] = (float) Transaction::whereBetween('created_at', [$start, $end])->where('amount', '<', 0)->sum('amount');
            }
            return ['labels' => $labels, 'credits' => $credits, 'debits' => array_map(fn($v) => abs($v), $debits)];
        }

        if ($period === 'year') {
            $labels = [];
            $credits = [];
            $debits = [];
            for ($i = 4; $i >= 0; $i--) {
                $year = Carbon::now()->subYears($i)->year;
                $labels[] = (string) $year;
                $credits[] = (float) Transaction::whereYear('created_at', $year)->where('amount', '>', 0)->sum('amount');
                $debits[] = (float) Transaction::whereYear('created_at', $year)->where('amount', '<', 0)->sum('amount');
            }
            return ['labels' => $labels, 'credits' => $credits, 'debits' => array_map(fn($v) => abs($v), $debits)];
        }

        $labels = ['الربع الأول', 'الربع الثاني', 'الربع الثالث', 'الربع الرابع'];
        $year = Carbon::now()->year;
        $credits = [];
        $debits = [];
        for ($q = 1; $q <= 4; $q++) {
            $credits[] = (float) Transaction::whereYear('created_at', $year)->whereRaw('QUARTER(created_at) = ?', [$q])->where('amount', '>', 0)->sum('amount');
            $debits[] = (float) Transaction::whereYear('created_at', $year)->whereRaw('QUARTER(created_at) = ?', [$q])->where('amount', '<', 0)->sum('amount');
        }
        return ['labels' => $labels, 'credits' => $credits, 'debits' => array_map(fn($v) => abs($v), $debits)];
    }

    private function getPeriodBounds($period)
    {
        $p = match ($period) {
            'month', 'monthly' => 'month',
            'quarter', 'quarterly' => 'quarter',
            'half', 'semiannual' => 'half',
            'year', 'annual' => 'year',
            default => null,
        };
        if (!$p) { return [null, null]; }
        if ($p === 'month') { return [Carbon::now()->subMonths(11)->startOfMonth(), Carbon::now()->endOfMonth()]; }
        if ($p === 'quarter' || $p === 'half' || $p === 'year') { return [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()]; }
        return [null, null];
    }
}