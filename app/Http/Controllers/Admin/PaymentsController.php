<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\PaymentCard;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentsController extends Controller
{
    public function index(Request $request)
    {
        // Selected period for financial operations summary
        $period = $request->get('period', 'monthly');
        // Range for payment card states
        $statesFrom = $request->get('states_from');
        $statesTo = $request->get('states_to');
        // Defaults for states range (current year to today)
        $defaultStatesFrom = Carbon::now()->startOfYear()->toDateString();
        $defaultStatesTo = Carbon::now()->toDateString();
        if (! $statesFrom || ! $statesTo) {
            $statesFrom = $defaultStatesFrom;
            $statesTo = $defaultStatesTo;
        }
        // Get statistics
        $stats = $this->getStatistics();

        // Get payment card states
        $paymentStates = $this->getPaymentStates();

        // Get financial operations summary based on selected period
        $financialOperations = $this->getFinancialOperations($period);

        // Get recent transactions
        $recentTransactions = $this->getRecentTransactions();

        // Provide payment cards updates data expected by the view
        $cardsUpdates = $this->getCardsUpdates();

        // Provide counts for payment card statuses (by selected range)
        $counts = $this->getCardCountsRange($statesFrom, $statesTo);

        return view('dashboard.payments.index', compact(
            'stats',
            'paymentStates',
            'financialOperations',
            'recentTransactions',
            'cardsUpdates',
            'counts'
        ))->with('selectedPeriod', $period)
            ->with('selectedStatesFrom', $statesFrom)
            ->with('selectedStatesTo', $statesTo);
    }

    private function getStatistics()
    {
        $totalRevenue = Transaction::where('type', 'credit')->sum('amount') ?: 71897;
        $totalBookings = Booking::count() ?: 30210;
        $totalCreditRequests = Transaction::where('type', 'withdrawal')->count() ?: 155;
        $totalWithdrawalOperations = Transaction::where('type', 'withdrawal')->where('status', 'completed')->count() ?: 1897;

        // Cards statistics for this dashboard
        $totalCards = PaymentCard::count() ?: 0;
        $totalIssuedAmount = PaymentCard::sum('amount') ?: 0;

        return [
            'total_revenue' => number_format($totalRevenue, 0),
            'total_bookings' => number_format($totalBookings, 0),
            'total_credit_requests' => number_format($totalCreditRequests, 0),
            'total_withdrawal_operations' => number_format($totalWithdrawalOperations, 0),

            // Keys used by the payments index view
            'total_cards' => number_format($totalCards, 0),
            'total_amount' => number_format($totalIssuedAmount, 0),
            'withdraw_requests' => number_format($totalCreditRequests, 0),
            'completed_payments' => number_format($totalWithdrawalOperations, 0),
        ];
    }

    private function getPaymentStates()
    {
        // Get counts for each state
        $pending = Booking::where('status', 'pending')->count();
        $approved = Booking::where('status', 'confirmed')->count();
        $cancelled = Booking::where('status', 'cancelled')->count();
        $completed = Booking::where('status', 'completed')->count();

        $total = $pending + $approved + $cancelled + $completed;

        // If no data, use sample data
        if ($total == 0) {
            return [
                'pending' => 30,
                'approved' => 25,
                'cancelled' => 20,
                'completed' => 25,
            ];
        }

        // Calculate percentages
        return [
            'pending' => round(($pending / $total) * 100),
            'approved' => round(($approved / $total) * 100),
            'cancelled' => round(($cancelled / $total) * 100),
            'completed' => round(($completed / $total) * 100),
        ];
    }

    private function getFinancialOperations(string $period = 'monthly')
    {
        $labels = [];
        $deposits = [];
        $withdrawals = [];

        switch ($period) {
            case 'all':
                // Group by all available years in the dataset
                $years = Transaction::select(DB::raw('YEAR(created_at) as y'))
                    ->distinct()
                    ->orderBy('y')
                    ->pluck('y')
                    ->toArray();

                if (! empty($years)) {
                    foreach ($years as $y) {
                        $labels[] = 'السنة '.$y;
                        $depositAmount = Transaction::where('type', 'credit')
                            ->whereYear('created_at', $y)
                            ->sum('amount');
                        $withdrawalAmount = Transaction::where('type', 'withdrawal')
                            ->whereYear('created_at', $y)
                            ->sum('amount');
                        $deposits[] = $depositAmount > 0 ? round($depositAmount / 1000) : 0;
                        $withdrawals[] = $withdrawalAmount > 0 ? round($withdrawalAmount / 1000) : 0;
                    }
                } else {
                    // Fallback sample for no data
                    $currentYear = Carbon::now()->year;
                    for ($y = $currentYear - 3; $y <= $currentYear; $y++) {
                        $labels[] = 'السنة '.$y;
                    }
                    $deposits = [400, 450, 500, 550];
                    $withdrawals = [300, 350, 375, 400];
                }
                break;
            case 'quarterly':
                $labels = ['الربع الأول', 'الربع الثاني', 'الربع الثالث', 'الربع الرابع'];
                for ($q = 1; $q <= 4; $q++) {
                    $depositAmount = Transaction::where('type', 'credit')
                        ->whereRaw('QUARTER(created_at) = ?', [$q])
                        ->whereYear('created_at', Carbon::now()->year)
                        ->sum('amount');

                    $withdrawalAmount = Transaction::where('type', 'withdrawal')
                        ->whereRaw('QUARTER(created_at) = ?', [$q])
                        ->whereYear('created_at', Carbon::now()->year)
                        ->sum('amount');

                    $deposits[] = $depositAmount > 0 ? round($depositAmount / 1000) : 0;
                    $withdrawals[] = $withdrawalAmount > 0 ? round($withdrawalAmount / 1000) : 0;
                }
                break;

            case 'semiannual':
                $labels = ['النصف الأول', 'النصف الثاني'];
                // H1: months 1-6, H2: months 7-12
                foreach ([[1, 6], [7, 12]] as [$start,$end]) {
                    $depositAmount = Transaction::where('type', 'credit')
                        ->whereYear('created_at', Carbon::now()->year)
                        ->whereBetween(DB::raw('MONTH(created_at)'), [$start, $end])
                        ->sum('amount');

                    $withdrawalAmount = Transaction::where('type', 'withdrawal')
                        ->whereYear('created_at', Carbon::now()->year)
                        ->whereBetween(DB::raw('MONTH(created_at)'), [$start, $end])
                        ->sum('amount');

                    $deposits[] = $depositAmount > 0 ? round($depositAmount / 1000) : 0;
                    $withdrawals[] = $withdrawalAmount > 0 ? round($withdrawalAmount / 1000) : 0;
                }
                break;

            case 'annual':
                // Last 4 years including current
                $currentYear = Carbon::now()->year;
                for ($y = $currentYear - 3; $y <= $currentYear; $y++) {
                    $labels[] = 'السنة '.$y;
                    $depositAmount = Transaction::where('type', 'credit')
                        ->whereYear('created_at', $y)
                        ->sum('amount');
                    $withdrawalAmount = Transaction::where('type', 'withdrawal')
                        ->whereYear('created_at', $y)
                        ->sum('amount');
                    $deposits[] = $depositAmount > 0 ? round($depositAmount / 1000) : 0;
                    $withdrawals[] = $withdrawalAmount > 0 ? round($withdrawalAmount / 1000) : 0;
                }
                break;

            case 'monthly':
            default:
                // Months of current year
                $labels = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
                for ($m = 1; $m <= 12; $m++) {
                    $depositAmount = Transaction::where('type', 'credit')
                        ->whereMonth('created_at', $m)
                        ->whereYear('created_at', Carbon::now()->year)
                        ->sum('amount');

                    $withdrawalAmount = Transaction::where('type', 'withdrawal')
                        ->whereMonth('created_at', $m)
                        ->whereYear('created_at', Carbon::now()->year)
                        ->sum('amount');

                    $deposits[] = $depositAmount > 0 ? round($depositAmount / 1000) : 0;
                    $withdrawals[] = $withdrawalAmount > 0 ? round($withdrawalAmount / 1000) : 0;
                }
                break;
        }

        // If no data at all, provide sample data to avoid empty chart
        if (array_sum($deposits) == 0 && array_sum($withdrawals) == 0) {
            if ($period === 'quarterly') {
                $labels = ['الربع الأول', 'الربع الثاني', 'الربع الثالث', 'الربع الرابع'];
                $deposits = [100, 175, 250, 50];
                $withdrawals = [100, 200, 200, 75];
            } elseif ($period === 'semiannual') {
                $labels = ['النصف الأول', 'النصف الثاني'];
                $deposits = [300, 275];
                $withdrawals = [250, 225];
            } elseif ($period === 'annual') {
                $currentYear = Carbon::now()->year;
                $labels = ['السنة '.($currentYear - 3), 'السنة '.($currentYear - 2), 'السنة '.($currentYear - 1), 'السنة '.$currentYear];
                $deposits = [400, 450, 500, 550];
                $withdrawals = [300, 350, 375, 400];
            } else { // monthly
                $labels = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
                $deposits = [50, 60, 45, 70, 80, 55, 65, 75, 85, 95, 100, 110];
                $withdrawals = [40, 50, 35, 60, 70, 45, 55, 65, 75, 85, 90, 100];
            }
        }

        return [
            'labels' => $labels,
            'deposits' => $deposits,
            'withdrawals' => $withdrawals,
        ];
    }

    // Range-based financial operations aggregation (monthly bins across range)
    private function getFinancialOperationsByRange(string $from, string $to)
    {
        $start = Carbon::parse($from)->startOfDay();
        $end = Carbon::parse($to)->endOfDay();
        if ($start->gt($end)) {
            [$start, $end] = [$end, $start];
        }

        $labels = [];
        $deposits = [];
        $withdrawals = [];

        $cursor = $start->copy()->startOfMonth();
        while ($cursor->lte($end)) {
            $monthStart = $cursor->copy()->startOfMonth();
            $monthEnd = $cursor->copy()->endOfMonth();
            if ($monthStart->lt($start)) {
                $monthStart = $start->copy();
            }
            if ($monthEnd->gt($end)) {
                $monthEnd = $end->copy();
            }

            $labels[] = $this->arabicMonthYear($cursor);

            $depositAmount = Transaction::where('type', 'credit')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('amount');
            $withdrawalAmount = Transaction::where('type', 'withdrawal')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('amount');

            $deposits[] = $depositAmount > 0 ? round($depositAmount / 1000) : 0;
            $withdrawals[] = $withdrawalAmount > 0 ? round($withdrawalAmount / 1000) : 0;

            $cursor->addMonth();
        }

        // Fallback sample to avoid empty chart
        if (array_sum($deposits) == 0 && array_sum($withdrawals) == 0) {
            if (count($labels) === 0) {
                $labels = [$this->arabicMonthYear($start)];
            }
            $deposits = [];
            $withdrawals = [];
            foreach ($labels as $i => $_) {
                $deposits[] = 50 + ($i * 10);
                $withdrawals[] = 40 + ($i * 8);
            }
        }

        return [
            'labels' => $labels,
            'deposits' => $deposits,
            'withdrawals' => $withdrawals,
        ];
    }

    // Range-based card counts
    private function getCardCountsRange(string $from, string $to)
    {
        $start = Carbon::parse($from)->startOfDay();
        $end = Carbon::parse($to)->endOfDay();
        if ($start->gt($end)) {
            [$start, $end] = [$end, $start];
        }

        $sold = PaymentCard::where(function ($q) {
            $q->whereNotNull('user_id')->orWhere('status', 'active');
        })
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('issue_date', [$start, $end])
                    ->orWhereBetween('created_at', [$start, $end]);
            })
            ->count();

        $charged = PaymentCard::where('balance', '>', 0)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('updated_at', [$start, $end])
                    ->orWhereBetween('issue_date', [$start, $end])
                    ->orWhereBetween('created_at', [$start, $end]);
            })
            ->count();

        $expired = PaymentCard::where('status', 'expired')
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('expiry_date', [$start, $end])
                    ->orWhereBetween('updated_at', [$start, $end]);
            })
            ->count();

        $cancelled = PaymentCard::where('status', 'cancelled')
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('updated_at', [$start, $end])
                    ->orWhereBetween('created_at', [$start, $end]);
            })
            ->count();

        return [
            'sold' => $sold,
            'charged' => $charged,
            'expired' => $expired,
            'cancelled' => $cancelled,
        ];
    }

    private function arabicMonthYear(Carbon $date)
    {
        $months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];

        return $months[$date->month - 1].' '.$date->year;
    }

    private function getCardsUpdates()
    {
        $cards = PaymentCard::latest()->take(10)->get();

        // Add aliases and Arabic status label required by the Blade
        $cards->each(function ($card) {
            $card->setAttribute('number', $card->card_number);
            $card->setAttribute('price', number_format($card->amount, 0));
            $card->setAttribute('status_ar', $this->arabicStatus($card->status));
        });

        return $cards;
    }

    private function arabicStatus($status)
    {
        switch ($status) {
            case 'pending':   return 'مصدر';
            case 'active':    return 'مشحون';
            case 'expired':   return 'منتهي';
            case 'cancelled': return 'ملغي';
            default:          return 'مصدر';
        }
    }

    private function getCardCounts(string $period = 'monthly')
    {
        // All-time counts without date filtering
        if ($period === 'all') {
            $sold = PaymentCard::where(function ($q) {
                $q->whereNotNull('user_id')->orWhere('status', 'active');
            })
                ->count();
            $charged = PaymentCard::where('balance', '>', 0)->count();
            $expired = PaymentCard::where('status', 'expired')->count();
            $cancelled = PaymentCard::where('status', 'cancelled')->count();

            return [
                'sold' => $sold,
                'charged' => $charged,
                'expired' => $expired,
                'cancelled' => $cancelled,
            ];
        }

        $now = Carbon::now();
        switch ($period) {
            case 'quarterly':
                $start = $now->copy()->startOfQuarter();
                $end = $now->copy()->endOfQuarter();
                break;
            case 'semiannual':
                if ($now->month <= 6) {
                    $start = Carbon::create($now->year, 1, 1)->startOfMonth();
                    $end = Carbon::create($now->year, 6, 1)->endOfMonth();
                } else {
                    $start = Carbon::create($now->year, 7, 1)->startOfMonth();
                    $end = Carbon::create($now->year, 12, 1)->endOfMonth();
                }
                break;
            case 'annual':
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                break;
            case 'monthly':
            default:
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
        }

        // "sold": cards assigned to a user or active issued within period
        $sold = PaymentCard::where(function ($q) {
            $q->whereNotNull('user_id')->orWhere('status', 'active');
        })
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('issue_date', [$start, $end])
                    ->orWhereBetween('created_at', [$start, $end]);
            })
            ->count();
        // Fall back to overall if no period data
        if ($sold === 0) {
            $sold = PaymentCard::where(function ($q) {
                $q->whereNotNull('user_id')->orWhere('status', 'active');
            })
                ->count();
        }

        // "charged": cards with positive balance updated/issued within period
        $charged = PaymentCard::where('balance', '>', 0)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('updated_at', [$start, $end])
                    ->orWhereBetween('issue_date', [$start, $end])
                    ->orWhereBetween('created_at', [$start, $end]);
            })
            ->count();

        // "expired": status expired with expiry_date within period (or recently updated)
        $expired = PaymentCard::where('status', 'expired')
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('expiry_date', [$start, $end])
                    ->orWhereBetween('updated_at', [$start, $end]);
            })
            ->count();

        // "cancelled": status cancelled updated/created within period
        $cancelled = PaymentCard::where('status', 'cancelled')
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('updated_at', [$start, $end])
                    ->orWhereBetween('created_at', [$start, $end]);
            })
            ->count();

        return [
            'sold' => $sold,
            'charged' => $charged,
            'expired' => $expired,
            'cancelled' => $cancelled,
        ];
    }

    private function getRecentTransactions()
    {
        $transactions = Transaction::with(['booking.user', 'wallet.user'])
            ->latest()
            ->take(10)
            ->get();

        // If no transactions, create sample data
        if ($transactions->isEmpty()) {
            return collect([
                [
                    'status' => 'خصم',
                    'amount' => '150 ج.م',
                    'date' => '2025/05/25 6:00 م',
                    'date_only' => '2025/05/25',
                    'time_only' => '6:00 م',
                    'user_name' => 'اسم المستخدم',
                    'user_email' => 'jane.cooper@example.com',
                    'user_avatar' => 'https://ui-avatars.com/api/?name=User&background=3B82F6&color=fff',
                ],
                [
                    'status' => 'إضافة',
                    'amount' => '150 ج.م',
                    'date' => '2025/05/25 6:00 م',
                    'date_only' => '2025/05/25',
                    'time_only' => '6:00 م',
                    'user_name' => 'اسم المستخدم',
                    'user_email' => 'jane.cooper@example.com',
                    'user_avatar' => 'https://ui-avatars.com/api/?name=User&background=10B981&color=fff',
                ],
                [
                    'status' => 'خصم',
                    'amount' => '150 ج.م',
                    'date' => '2025/05/25 6:00 م',
                    'date_only' => '2025/05/25',
                    'time_only' => '6:00 م',
                    'user_name' => 'اسم المستخدم',
                    'user_email' => 'jane.cooper@example.com',
                    'user_avatar' => 'https://ui-avatars.com/api/?name=User&background=3B82F6&color=fff',
                ],
                [
                    'status' => 'إضافة',
                    'amount' => '150 ج.م',
                    'date' => '2025/05/25 6:00 م',
                    'date_only' => '2025/05/25',
                    'time_only' => '6:00 م',
                    'user_name' => 'اسم المستخدم',
                    'user_email' => 'jane.cooper@example.com',
                    'user_avatar' => 'https://ui-avatars.com/api/?name=User&background=10B981&color=fff',
                ],
                [
                    'status' => 'خصم',
                    'amount' => '150 ج.م',
                    'date' => '2025/05/25 6:00 م',
                    'date_only' => '2025/05/25',
                    'time_only' => '6:00 م',
                    'user_name' => 'اسم المستخدم',
                    'user_email' => 'jane.cooper@example.com',
                    'user_avatar' => 'https://ui-avatars.com/api/?name=User&background=3B82F6&color=fff',
                ],
            ]);
        }

        return $transactions->map(function ($transaction) {
            $user = $transaction->booking ? $transaction->booking->user : ($transaction->wallet ? $transaction->wallet->user : null);

            $dateOnly = $transaction->created_at->format('Y/m/d');
            $timeOnly = $transaction->created_at->format('g:i A');
            // تحويل AM/PM إلى العربية
            $timeOnly = str_replace(['AM', 'PM'], ['ص', 'م'], $timeOnly);

            return [
                'status' => $transaction->type === 'credit' ? 'إضافة' : 'خصم',
                'amount' => number_format($transaction->amount, 0).' ج.م',
                'date' => $transaction->created_at->format('Y/m/d g:i A'),
                'date_only' => $dateOnly,
                'time_only' => $timeOnly,
                'user_name' => $user ? $user->name : 'اسم المستخدم',
                'user_email' => $user ? $user->email : 'email@example.com',
                'user_avatar' => $user && $user->avatar ? asset($user->avatar) : ($user ? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=3B82F6&color=fff' : 'https://ui-avatars.com/api/?name=User&background=3B82F6&color=fff'),
            ];
        });
    }
}
