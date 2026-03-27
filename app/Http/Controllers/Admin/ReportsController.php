<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Complaint;
use App\Models\SupportTicket;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        // Get date range from request or default to last 12 months
        $period = $request->get('period', 'year');
        [$start, $end] = $this->getBounds($request);

        // Statistics Cards
        $stats = $this->getStatistics();

        $userGrowthData = $this->getUserGrowthData($period, $start, $end);

        $bookingData = $this->getBookingData($period, $start, $end);

        $revenueData = $this->getRevenueData($period, $start, $end);

        // Customer Complaints Types
        $complaintTypes = $this->getComplaintTypesData();

        // Landlord Complaints Types
        $landlordComplaintTypes = $this->getLandlordComplaintTypesData();

        // Complaint Resolution Status
        $complaintResolutionData = $this->getComplaintResolutionData();

        $complaintSubmissionData = $this->getComplaintSubmissionData($period, $start, $end);
        $complaintSubmissionRatio = $this->getComplaintSubmissionRatio($start, $end);
        $complaintCategoriesTenants = $this->getTicketCategoryDistribution('tenant');
        $complaintCategoriesLandlords = $this->getTicketCategoryDistribution('landlord');
        $complaintResolutionByCategory = $this->getTicketResolutionByCategory();

        // Top Active Places by Bookings
        $topActivePlacesData = $this->getTopActivePlacesBookingData();

        return view('dashboard.reports.index', compact(
            'stats',
            'userGrowthData',
            'bookingData',
            'revenueData',
            'complaintTypes',
            'landlordComplaintTypes',
            'complaintResolutionData',
            'complaintSubmissionData',
            'complaintSubmissionRatio',
            'topActivePlacesData',
            'complaintCategoriesTenants',
            'complaintCategoriesLandlords',
            'complaintResolutionByCategory',
            'period'
        ));
    }

    private function getBounds(Request $request)
    {
        $period = $request->get('period', 'year');
        if ($period === 'custom' && $request->filled(['from'])) {
            try {
                $start = Carbon::parse($request->get('from'))->startOfDay();
            } catch (\Throwable $e) {
                $start = Carbon::now()->startOfMonth();
            }
            if ($request->filled('to')) {
                try { $end = Carbon::parse($request->get('to'))->endOfDay(); }
                catch (\Throwable $e) { $end = Carbon::now()->endOfMonth(); }
            } else {
                $end = Carbon::parse($request->get('from'))->endOfDay();
            }
            return [$start, $end];
        }
        return $this->getPeriodBounds($period);
    }

    private function getPeriodBounds($period)
    {
        $p = match ($period) {
            'month', 'monthly' => 'month',
            'quarter', 'quarterly' => 'quarter',
            'half', 'semiannual' => 'half',
            default => 'year',
        };
        if ($p === 'month') {
            $start = Carbon::now()->subMonths(11)->startOfMonth();
            $end = Carbon::now()->endOfMonth();
            return [$start, $end];
        }
        if ($p === 'quarter' || $p === 'half') {
            $y = Carbon::now()->year;
            $start = Carbon::create($y, 1, 1)->startOfDay();
            $end = Carbon::create($y, 12, 31)->endOfDay();
            return [$start, $end];
        }
        $start = Carbon::now()->subYears(4)->startOfYear();
        $end = Carbon::now()->endOfYear();
        return [$start, $end];
    }

    private function getStatistics()
    {
        $confirmedBookings = Booking::where('status', 'confirmed')->with('property')->get();
        $bookingsValue = $confirmedBookings->sum(function ($b) {
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

        return [
            'total_revenue' => number_format(Transaction::sum('amount') ?? 0, 0),
            'active_bookings' => Booking::where('status', 'confirmed')->count(),
            'booking_value' => (int) round($bookingsValue),
            'booking_requests' => Booking::where('status', 'pending')->count(),
            'complaints' => SupportTicket::count(),
            'total_users' => User::count(),
        ];
    }

    private function getUserGrowthData($period, $start = null, $end = null)
    {
        $period = match ($period) {
            'month', 'monthly' => 'month',
            'quarter', 'quarterly' => 'quarter',
            'half', 'semiannual' => 'half',
            'custom' => 'custom',
            default => 'year',
        };

        if ($period === 'custom' && $start && $end) {
            $labels = [];
            $tenants = [];
            $landlords = [];
            $cursor = $start->copy();
            while ($cursor->lte($end)) {
                $labels[] = $cursor->locale('ar')->translatedFormat('j M');
                $dayStart = $cursor->copy()->startOfDay();
                $dayEnd = $cursor->copy()->endOfDay();
                $tenants[] = User::where('user_type', 'tenant')->whereBetween('created_at', [$dayStart, $dayEnd])->count();
                $landlords[] = User::where('user_type', 'landlord')->whereBetween('created_at', [$dayStart, $dayEnd])->count();
                $cursor->addDay();
            }
            return ['labels' => $labels, 'tenants' => $tenants, 'landlords' => $landlords];
        }

        if ($period === 'month') {
            $labels = [];
            $tenants = [];
            $landlords = [];
            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $labels[] = $date->locale('ar')->translatedFormat('F');
                $monthStart = $date->copy()->startOfMonth();
                $monthEnd = $date->copy()->endOfMonth();
                $tenants[] = User::where('user_type', 'tenant')->whereBetween('created_at', [$monthStart, $monthEnd])->count();
                $landlords[] = User::where('user_type', 'landlord')->whereBetween('created_at', [$monthStart, $monthEnd])->count();
            }
            return [
                'labels' => $labels,
                'tenants' => $tenants,
                'landlords' => $landlords,
            ];
        }

        if ($period === 'quarter') {
            $labels = ['الربع الأول', 'الربع الثاني', 'الربع الثالث', 'الربع الرابع'];
            $year = Carbon::now()->year;
            $tenants = [];
            $landlords = [];
            for ($q = 1; $q <= 4; $q++) {
                $tenants[] = User::where('user_type', 'tenant')->whereYear('created_at', $year)->whereRaw('QUARTER(created_at) = ?', [$q])->count();
                $landlords[] = User::where('user_type', 'landlord')->whereYear('created_at', $year)->whereRaw('QUARTER(created_at) = ?', [$q])->count();
            }
            return [
                'labels' => $labels,
                'tenants' => $tenants,
                'landlords' => $landlords,
            ];
        }

        if ($period === 'half') {
            $labels = ['النصف الأول', 'النصف الثاني'];
            $year = Carbon::now()->year;
            $tenants = [
                User::where('user_type', 'tenant')->whereYear('created_at', $year)->whereMonth('created_at', '>=', 1)->whereMonth('created_at', '<=', 6)->count(),
                User::where('user_type', 'tenant')->whereYear('created_at', $year)->whereMonth('created_at', '>=', 7)->whereMonth('created_at', '<=', 12)->count(),
            ];
            $landlords = [
                User::where('user_type', 'landlord')->whereYear('created_at', $year)->whereMonth('created_at', '>=', 1)->whereMonth('created_at', '<=', 6)->count(),
                User::where('user_type', 'landlord')->whereYear('created_at', $year)->whereMonth('created_at', '>=', 7)->whereMonth('created_at', '<=', 12)->count(),
            ];
            return [
                'labels' => $labels,
                'tenants' => $tenants,
                'landlords' => $landlords,
            ];
        }

        $labels = [];
        $tenants = [];
        $landlords = [];
        for ($i = 4; $i >= 0; $i--) {
            $year = Carbon::now()->subYears($i)->year;
            $labels[] = (string) $year;
            $tenants[] = User::where('user_type', 'tenant')->whereYear('created_at', $year)->count();
            $landlords[] = User::where('user_type', 'landlord')->whereYear('created_at', $year)->count();
        }
        return [
            'labels' => $labels,
            'tenants' => $tenants,
            'landlords' => $landlords,
        ];
    }

    private function getBookingData($period, $start = null, $end = null)
    {
        $period = match ($period) {
            'month', 'monthly' => 'month',
            'quarter', 'quarterly' => 'quarter',
            'half', 'semiannual' => 'half',
            'custom' => 'custom',
            default => 'year',
        };

        if ($period === 'custom' && $start && $end) {
            $labels = [];
            $tenants = [];
            $landlords = [];
            $cursor = $start->copy();
            while ($cursor->lte($end)) {
                $labels[] = $cursor->locale('ar')->translatedFormat('j M');
                $dayStart = $cursor->copy()->startOfDay();
                $dayEnd = $cursor->copy()->endOfDay();
                $tenants[] = Booking::whereHas('user', function ($q) { $q->where('user_type', 'tenant'); })
                    ->whereBetween('created_at', [$dayStart, $dayEnd])->count();
                $landlords[] = Booking::whereHas('property.user', function ($q) { $q->where('user_type', 'landlord'); })
                    ->whereBetween('created_at', [$dayStart, $dayEnd])->count();
                $cursor->addDay();
            }
            return ['labels' => $labels, 'tenants' => $tenants, 'landlords' => $landlords];
        }

        if ($period === 'month') {
            $labels = [];
            $tenants = [];
            $landlords = [];
            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $labels[] = $date->locale('ar')->translatedFormat('F');
                $start = $date->copy()->startOfMonth();
                $end = $date->copy()->endOfMonth();
                $tenants[] = Booking::whereHas('user', function ($q) { $q->where('user_type', 'tenant'); })
                    ->whereBetween('created_at', [$start, $end])->count();
                $landlords[] = Booking::whereHas('property.user', function ($q) { $q->where('user_type', 'landlord'); })
                    ->whereBetween('created_at', [$start, $end])->count();
            }
            return [
                'labels' => $labels,
                'tenants' => $tenants,
                'landlords' => $landlords,
            ];
        }

        if ($period === 'quarter') {
            $labels = ['الربع الأول', 'الربع الثاني', 'الربع الثالث', 'الربع الرابع'];
            $year = Carbon::now()->year;
            $tenants = [];
            $landlords = [];
            for ($q = 1; $q <= 4; $q++) {
                $tenants[] = Booking::whereHas('user', function ($q1) { $q1->where('user_type', 'tenant'); })
                    ->whereYear('created_at', $year)
                    ->whereRaw('QUARTER(created_at) = ?', [$q])
                    ->count();
                $landlords[] = Booking::whereHas('property.user', function ($q2) { $q2->where('user_type', 'landlord'); })
                    ->whereYear('created_at', $year)
                    ->whereRaw('QUARTER(created_at) = ?', [$q])
                    ->count();
            }
            return [
                'labels' => $labels,
                'tenants' => $tenants,
                'landlords' => $landlords,
            ];
        }

        if ($period === 'half') {
            $labels = ['النصف الأول', 'النصف الثاني'];
            $year = Carbon::now()->year;
            $h1Start = Carbon::create($year, 1, 1)->startOfDay();
            $h1End = Carbon::create($year, 6, 30)->endOfDay();
            $h2Start = Carbon::create($year, 7, 1)->startOfDay();
            $h2End = Carbon::create($year, 12, 31)->endOfDay();

            $tenants = [
                Booking::whereHas('user', function ($q) { $q->where('user_type', 'tenant'); })
                    ->whereBetween('created_at', [$h1Start, $h1End])->count(),
                Booking::whereHas('user', function ($q) { $q->where('user_type', 'tenant'); })
                    ->whereBetween('created_at', [$h2Start, $h2End])->count(),
            ];
            $landlords = [
                Booking::whereHas('property.user', function ($q) { $q->where('user_type', 'landlord'); })
                    ->whereBetween('created_at', [$h1Start, $h1End])->count(),
                Booking::whereHas('property.user', function ($q) { $q->where('user_type', 'landlord'); })
                    ->whereBetween('created_at', [$h2Start, $h2End])->count(),
            ];

            return [
                'labels' => $labels,
                'tenants' => $tenants,
                'landlords' => $landlords,
            ];
        }

        $labels = [];
        $tenants = [];
        $landlords = [];
        for ($i = 4; $i >= 0; $i--) {
            $year = Carbon::now()->subYears($i)->year;
            $labels[] = (string) $year;
            $tenants[] = Booking::whereHas('user', function ($q) { $q->where('user_type', 'tenant'); })
                ->whereYear('created_at', $year)->count();
            $landlords[] = Booking::whereHas('property.user', function ($q) { $q->where('user_type', 'landlord'); })
                ->whereYear('created_at', $year)->count();
        }

        return [
            'labels' => $labels,
            'tenants' => $tenants,
            'landlords' => $landlords,
        ];
    }

    private function getRevenueData($period, $start = null, $end = null)
    {
        $period = match ($period) {
            'month', 'monthly' => 'month',
            'quarter', 'quarterly' => 'quarter',
            'half', 'semiannual' => 'half',
            'custom' => 'custom',
            default => 'year',
        };

        if ($period === 'custom' && $start && $end) {
            $labels = [];
            $values = [];
            $cursor = $start->copy();
            while ($cursor->lte($end)) {
                $labels[] = $cursor->locale('ar')->translatedFormat('j M');
                $dayStart = $cursor->copy()->startOfDay();
                $dayEnd = $cursor->copy()->endOfDay();
                $values[] = (float) Transaction::whereBetween('created_at', [$dayStart, $dayEnd])->sum('amount');
                $cursor->addDay();
            }
            return ['labels' => $labels, 'revenue' => $values];
        }

        if ($period === 'month') {
            $labels = [];
            $values = [];
            for ($i = 11; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $labels[] = $date->locale('ar')->translatedFormat('F');
                $start = $date->copy()->startOfMonth();
                $end = $date->copy()->endOfMonth();
                $values[] = (float) Transaction::whereBetween('created_at', [$start, $end])->sum('amount');
            }
            return ['labels' => $labels, 'revenue' => $values];
        }

        if ($period === 'quarter') {
            $labels = ['الربع الأول', 'الربع الثاني', 'الربع الثالث', 'الربع الرابع'];
            $year = Carbon::now()->year;
            $values = [];
            for ($q = 1; $q <= 4; $q++) {
                $values[] = (float) Transaction::whereYear('created_at', $year)
                    ->whereRaw('QUARTER(created_at) = ?', [$q])
                    ->sum('amount');
            }
            return ['labels' => $labels, 'revenue' => $values];
        }

        if ($period === 'half') {
            $labels = ['النصف الأول', 'النصف الثاني'];
            $year = Carbon::now()->year;
            $h1Start = Carbon::create($year, 1, 1)->startOfDay();
            $h1End = Carbon::create($year, 6, 30)->endOfDay();
            $h2Start = Carbon::create($year, 7, 1)->startOfDay();
            $h2End = Carbon::create($year, 12, 31)->endOfDay();
            $values = [
                (float) Transaction::whereBetween('created_at', [$h1Start, $h1End])->sum('amount'),
                (float) Transaction::whereBetween('created_at', [$h2Start, $h2End])->sum('amount'),
            ];
            return ['labels' => $labels, 'revenue' => $values];
        }

        $labels = [];
        $values = [];
        for ($i = 4; $i >= 0; $i--) {
            $year = Carbon::now()->subYears($i)->year;
            $labels[] = (string) $year;
            $values[] = (float) Transaction::whereYear('created_at', $year)->sum('amount');
        }
        return ['labels' => $labels, 'revenue' => $values];
    }

    private function getComplaintTypesData()
    {
        $types = [
            'عدم الاستجابة' => SupportTicket::where('category', 'عدم الاستجابة')->count(),
            'سلوك غير لائق' => SupportTicket::where('category', 'سلوك غير لائق')->count(),
            'محاولة فرض رسوم إضافية' => SupportTicket::where('category', 'محاولة فرض رسوم إضافية')->count(),
            'التأخر في تسليم العقار' => SupportTicket::where('category', 'التأخر في تسليم العقار')->count(),
        ];

        // بيانات ديناميكية بالكامل دون عينات
        
        return $types;
    }

    private function getLandlordComplaintTypesData()
    {
        $types = [
            'إتلاف الممتلكات' => Complaint::where('subject', 'LIKE', '%إتلاف%')->count(),
            'سلوك غير لائق' => Complaint::where('subject', 'LIKE', '%سلوك%')->count(),
            'عدم الالتزام باللوائح' => Complaint::where('subject', 'LIKE', '%التزام%')->count(),
            'التأخر في تسليم العقار' => Complaint::where('subject', 'LIKE', '%تأخر%')->count(),
        ];

        // بيانات ديناميكية بالكامل دون عينات
        
        return $types;
    }

    private function getComplaintResolutionData()
    {
        $tenants = [
            'resolved' => SupportTicket::whereHas('user', function ($q) {
                $q->where('user_type', 'tenant');
            })->where('status', 'resolved')->count(),
            'pending' => SupportTicket::whereHas('user', function ($q) {
                $q->where('user_type', 'tenant');
            })->where('status', 'open')->count(),
        ];

        $landlords = [
            'resolved' => Complaint::whereHas('user', function ($q) {
                $q->where('user_type', 'landlord');
            })->where('status', 'resolved')->count(),
            'pending' => Complaint::whereHas('user', function ($q) {
                $q->where('user_type', 'landlord');
            })->where('status', 'open')->count(),
        ];

        // تحويل إلى نسب مئوية اعتمادًا على البيانات الفعلية
        if (array_sum($tenants) > 0) {
            $total = array_sum($tenants);
            $tenants = [
                'resolved' => round(($tenants['resolved'] / $total) * 100),
                'pending' => round(($tenants['pending'] / $total) * 100),
            ];
        } else {
            $tenants = ['resolved' => 0, 'pending' => 0];
        }

        if (array_sum($landlords) > 0) {
            $total = array_sum($landlords);
            $landlords = [
                'resolved' => round(($landlords['resolved'] / $total) * 100),
                'pending' => round(($landlords['pending'] / $total) * 100),
            ];
        } else {
            $landlords = ['resolved' => 0, 'pending' => 0];
        }

        return [
            'tenants' => $tenants,
            'landlords' => $landlords,
        ];
    }

    private function getComplaintSubmissionData($period, $start = null, $end = null)
    {
        $categories = SupportTicket::query()
            ->select('category')
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->toArray();

        $result = [];
        if (!$start || !$end) { [$start, $end] = $this->getPeriodBounds($period); }
        foreach ($categories as $cat) {
            $resolved = SupportTicket::where('category', $cat)
                ->whereBetween('created_at', [$start, $end])
                ->where('status', 'resolved')->count();
            $pending = SupportTicket::where('category', $cat)
                ->whereBetween('created_at', [$start, $end])
                ->whereIn('status', ['open', 'in_progress'])->count();
            $rejected = SupportTicket::where('category', $cat)
                ->whereBetween('created_at', [$start, $end])
                ->where('status', 'closed')->count();

            $total = $resolved + $pending + $rejected;
            if ($total > 0) {
                $result[$cat] = [
                    'resolved' => round(($resolved / $total) * 100),
                    'pending' => round(($pending / $total) * 100),
                    'rejected' => round(($rejected / $total) * 100),
                ];
            } else {
                $result[$cat] = ['resolved' => 0, 'pending' => 0, 'rejected' => 0];
            }
        }

        return $result;
    }

    private function getComplaintSubmissionRatio($start, $end)
    {
        $tenants = SupportTicket::whereHas('user', function ($q) {
            $q->where('user_type', 'tenant');
        })->whereBetween('created_at', [$start, $end])->count();

        $landlords = SupportTicket::whereHas('user', function ($q) {
            $q->where('user_type', 'landlord');
        })->whereBetween('created_at', [$start, $end])->count();

        $total = $tenants + $landlords;
        $percentages = $total > 0
            ? [
                (int) round(($tenants / $total) * 100),
                (int) round(($landlords / $total) * 100),
            ]
            : [0, 0];

        return [
            'labels' => ['المستأجرين', 'المؤجرين'],
            'counts' => [$tenants, $landlords],
            'percentages' => $percentages,
        ];
    }

    private function getTopActivePlacesBookingData()
    {
        // استبدال الاعتماد على جدول active_places بجدول properties الموجود فعليًا
        $query = DB::table('properties as p')
            ->leftJoin('bookings as b', 'b.property_id', '=', 'p.id');

        // فلترة المنشور/المعتمد حسب الأعمدة المتوفرة في جدول properties
        if (Schema::hasColumn('properties', 'status')) {
            $query->where('p.status', 'published');
        } else {
            $query->where('p.approved', true);
        }

        $rows = $query
            ->selectRaw('p.id, p.title as name, p.city, p.price, COUNT(b.id) as total')
            ->whereIn('b.status', ['pending', 'confirmed'])
            ->groupBy('p.id', 'p.title', 'p.city', 'p.price')
            ->orderByDesc('total')
            ->orderByDesc('p.price')
            ->limit(4)
            ->get();

        $labels = $rows->map(function ($r) { return $r->name; })->toArray();
        $counts = $rows->map(function ($r) { return (int) $r->total; })->toArray();
        $sum = array_sum($counts);
        $percentages = $sum > 0
            ? array_map(fn($c) => round(($c / $sum) * 100), $counts)
            : array_map(fn() => 0, $counts);

        return [
            'labels' => $labels,
            'counts' => $counts,
            'percentages' => $percentages,
        ];
    }

    private function getTicketCategoryDistribution(string $userType)
    {
        $map = [
            'landlord' => [
                'إتلاف الممتلكات',
                'سلوك غير لائق',
                'عدم الالتزام باللوائح',
                'التأخر في تسليم العقار',
            ],
            'tenant' => [
                'عدم الاستجابة',
                'سلوك غير لائق',
                'محاولة فرض رسوم إضافية',
                'التأخر في تسليم العقار',
            ],
        ];

        $labels = $map[$userType] ?? [];
        $counts = [];
        foreach ($labels as $cat) {
            $counts[] = (int) SupportTicket::whereHas('user', function ($q) use ($userType) {
                    $q->where('user_type', $userType);
                })
                ->where('category', $cat)
                ->count();
        }

        $sum = array_sum($counts);
        if ($sum === 0) {
            $rows = SupportTicket::query()
                ->whereHas('user', function ($q) use ($userType) { $q->where('user_type', $userType); })
                ->whereNotNull('category')
                ->selectRaw('category, COUNT(*) as total')
                ->groupBy('category')
                ->orderByDesc('total')
                ->limit(4)
                ->get();
            $labels = $rows->pluck('category')->toArray();
            $counts = $rows->pluck('total')->map(fn($v) => (int) $v)->toArray();
            $sum = array_sum($counts);
        }

        $percentages = $sum > 0
            ? array_map(fn($c) => (int) round(($c / $sum) * 100), $counts)
            : array_fill(0, count($counts), 0);

        return [
            'labels' => $labels,
            'counts' => $counts,
            'percentages' => $percentages,
        ];
    }

    private function getTicketResolutionByCategory()
    {
        $topCategories = SupportTicket::query()
            ->whereNotNull('category')
            ->selectRaw('category, COUNT(*) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->limit(4)
            ->pluck('category')
            ->toArray();

        $result = [];
        foreach ($topCategories as $cat) {
            $resolved = SupportTicket::where('category', $cat)->where('status', 'resolved')->count();
            $pending = SupportTicket::where('category', $cat)->whereIn('status', ['open', 'in_progress'])->count();
            $rejected = SupportTicket::where('category', $cat)->where('status', 'closed')->count();

            $total = $resolved + $pending + $rejected;
            if ($total > 0) {
                $result[$cat] = [
                    'resolved' => round(($resolved / $total) * 100),
                    'pending' => round(($pending / $total) * 100),
                    'rejected' => round(($rejected / $total) * 100),
                ];
            } else {
                $result[$cat] = [
                    'resolved' => 0,
                    'pending' => 0,
                    'rejected' => 0,
                ];
            }
        }

        return $result;
    }
}
