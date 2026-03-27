<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penalty;
use App\Models\Setting;
use Illuminate\Http\Request;

class PenaltiesController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $type = $request->get('type');
        $search = $request->get('search');
        $userType = $request->get('user_type');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $amountMin = $request->get('amount_min');
        $amountMax = $request->get('amount_max');
        $perPage = (int) ($request->get('per_page') ?: 10);
        $perPage = $perPage > 0 && $perPage <= 100 ? $perPage : 10;

        $query = Penalty::with(['user', 'booking']);

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($type && $type !== 'all') {
            $query->where('type', $type);
        }

        if ($userType && $userType !== 'all') {
            $query->whereHas('user', function ($q) use ($userType) {
                $q->where('user_type', $userType);
            });
        }

        if ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->where('created_at', '<=', $dateTo);
        }

        if ($amountMin !== null && $amountMin !== '') {
            $query->where('amount', '>=', (float) $amountMin);
        }

        if ($amountMax !== null && $amountMax !== '') {
            $query->where('amount', '<=', (float) $amountMax);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($qq) use ($search) {
                    $qq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                    ->orWhere('reason', 'like', "%{$search}%");
            });
        }

        $penalties = $query->orderByDesc('created_at')->paginate($perPage)->withQueryString();
        $stats = $this->getStatistics();

        $settings = [
            'compensation_method' => Setting::get('compensation_method', 'percentage'),
            'compensation_percentage' => (float) (Setting::get('compensation_percentage', 0) ?: 0),
            'compensation_fixed_extra' => (float) (Setting::get('compensation_fixed_extra', 0) ?: 0),
            'cancel_penalty_method' => Setting::get('cancel_penalty_method', 'fixed'),
            'cancel_penalty_percentage' => (float) (Setting::get('cancel_penalty_percentage', 0) ?: 0),
            'cancel_penalty_fixed_value' => (float) (Setting::get('cancel_penalty_fixed_value', 0) ?: 0),
        ];

        return view('dashboard.payments.stats', compact('penalties', 'stats', 'settings'));
    }

    private function getStatistics()
    {
        $totalPaidPenalties = Penalty::where('status', 'paid')
            ->where('type', '!=', 'compensation')
            ->sum('amount') ?: 0;
        $totalPaidCompensations = Penalty::where('type', 'compensation')
            ->where('status', 'paid')
            ->sum('amount') ?: 0;
        $pendingOperations = Penalty::where('status', 'pending')->count() ?: 0;

        return [
            'total_paid_penalties' => $totalPaidPenalties,
            'total_paid_compensations' => $totalPaidCompensations,
            'pending_operations' => $pendingOperations,
        ];
    }

    public function show($id)
    {
        $penalty = Penalty::with(['user.wallet', 'booking'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'penalty' => $penalty,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $penalty = Penalty::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,paid,cancelled',
        ]);

        $penalty->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة الغرامة بنجاح',
            'penalty' => $penalty,
        ]);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'compensation_method' => 'nullable|in:percentage,full,fixed_extra',
            'compensation_percentage' => 'nullable|numeric|min:0|max:100',
            'compensation_fixed_extra' => 'nullable|numeric|min:0',
            'cancel_penalty_method' => 'nullable|in:percentage,fixed',
            'cancel_penalty_percentage' => 'nullable|numeric|min:0|max:100',
            'cancel_penalty_fixed_value' => 'nullable|numeric|min:0',
        ]);

        foreach ($validated as $key => $value) {
            if ($value !== null) {
                Setting::set($key, $value);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'تم حفظ الإعدادات بنجاح',
        ]);
    }

    public function preview($id)
    {
        return view('dashboard.payments.penalties-preview', ['id' => (int) $id]);
    }

    public function apiIndex(Request $request)
    {
        $status = $request->get('status');
        $type = $request->get('type');
        $userId = $request->get('user_id');
        $search = $request->get('search');
        $perPage = (int) ($request->get('per_page') ?: 10);
        $perPage = $perPage > 0 && $perPage <= 100 ? $perPage : 10;

        $query = Penalty::with(['user.wallet', 'booking']);

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }
        if ($type && $type !== 'all') {
            $query->where('type', $type);
        }
        if ($userId) {
            $query->where('user_id', (int) $userId);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($qq) use ($search) {
                    $qq->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                    ->orWhere('reason', 'like', "%{$search}%");
            });
        }

        $penalties = $query->orderByDesc('created_at')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $penalties->items(),
            'meta' => [
                'current_page' => $penalties->currentPage(),
                'last_page' => $penalties->lastPage(),
                'per_page' => $penalties->perPage(),
                'total' => $penalties->total(),
            ],
        ]);
    }

    public function apiStore(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'booking_id' => 'nullable|exists:bookings,id',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:late_payment,damage,cancellation,violation,compensation',
            'reason' => 'nullable|string',
            'status' => 'nullable|in:pending,paid,cancelled',
            'notes' => 'nullable|string',
        ]);

        if (!isset($validated['status'])) {
            $validated['status'] = 'pending';
        }

        $penalty = Penalty::create($validated);
        $penalty->load(['user.wallet', 'booking']);

        return response()->json([
            'success' => true,
             'penalty' => $penalty,
        ], 201);
    }

    public function apiUpdate(Request $request, $id)
    {
        $penalty = Penalty::findOrFail($id);

        $validated = $request->validate([
            'booking_id' => 'nullable|exists:bookings,id',
            'amount' => 'nullable|numeric|min:0',
            'type' => 'nullable|in:late_payment,damage,cancellation,violation,compensation',
            'reason' => 'nullable|string',
            'status' => 'nullable|in:pending,paid,cancelled',
            'paid_at' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $penalty->update($validated);
        $penalty->load(['user.wallet', 'booking']);

        return response()->json([
            'success' => true,
            'penalty' => $penalty,
        ]);
    }

    public function apiDestroy($id)
    {
        $penalty = Penalty::findOrFail($id);
        $penalty->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    public function apiClear(Request $request)
    {
        $confirm = $request->query('confirm');
        if ($confirm !== 'DELETE_ALL') {
            return response()->json([
                'success' => false,
                'message' => 'يرجى تمرير confirm=DELETE_ALL لمسح كل البيانات',
            ], 400);
        }

        $count = Penalty::count();
        Penalty::query()->delete();

        return response()->json([
            'success' => true,
            'deleted' => $count,
        ]);
    }

    public function apiCancelPenaltySettings()
    {
        $method = Setting::get('cancel_penalty_method', 'fixed');
        $percent = (float) (Setting::get('cancel_penalty_percentage', 0) ?: 0);
        $fixed = (float) (Setting::get('cancel_penalty_fixed_value', 0) ?: 0);

        return response()->json([
            'success' => true,
            'data' => [
                'method' => $method,
                'percentage' => $percent,
                'fixed_value' => $fixed,
            ],
        ]);
    }

    public function apiCompensationSettings()
    {
        $method = Setting::get('compensation_method', 'percentage');
        $percent = (float) (Setting::get('compensation_percentage', 0) ?: 0);
        $fixedExtra = (float) (Setting::get('compensation_fixed_extra', 0) ?: 0);

        return response()->json([
            'success' => true,
            'data' => [
                'method' => $method,
                'percentage' => $percent,
                'fixed_extra' => $fixedExtra,
            ],
        ]);
    }

    public function apiApplyCompensation(Request $request)
    {
        $data = $request->validate([
            'booking_id' => ['required', 'exists:bookings,id'],
            'party' => ['nullable', 'in:landlord,tenant'],
            'mark_paid' => ['nullable', 'boolean'],
            'reason' => ['nullable', 'string'],
        ]);

        $booking = \App\Models\Booking::with(['property.user', 'user'])->findOrFail($data['booking_id']);
        $dailyPrice = (float) ($booking->property?->price ?? 0);
        $days = \Carbon\Carbon::parse($booking->start_date)->diffInDays(\Carbon\Carbon::parse($booking->end_date));
        $days = max(1, $days);
        $total = round($dailyPrice * $days, 2);

        $method = Setting::get('compensation_method', 'percentage');
        $percent = (float) (Setting::get('compensation_percentage', 0) ?: 0);
        $fixedExtra = (float) (Setting::get('compensation_fixed_extra', 0) ?: 0);

        $amount = 0.0;
        if ($method === 'percentage') {
            $amount = round($total * ($percent / 100), 2);
        } elseif ($method === 'full') {
            $amount = $total;
        } elseif ($method === 'fixed_extra') {
            $amount = round($fixedExtra, 2);
        }
        $amount = max(0, min($amount, $total));

        $party = $data['party'] ?? null;
        if (! $party) {
            if ($booking->canceled_by === 'renter') {
                $party = 'landlord';
            } elseif ($booking->canceled_by === 'owner') {
                $party = 'tenant';
            } else {
                $authUser = \Illuminate\Support\Facades\Auth::user();
                $authType = strtolower((string) ($authUser?->user_type ?? 'tenant'));
                $party = in_array($authType, ['landlord', 'both'], true) ? 'landlord' : 'tenant';
            }
        }

        $receiver = $party === 'landlord' ? ($booking->property?->user) : $booking->user;
        $payer = $party === 'landlord' ? $booking->user : ($booking->property?->user);

        $pen = Penalty::create([
            'user_id' => $receiver?->id,
            'booking_id' => $booking->id,
            'amount' => $amount,
            'type' => 'compensation',
            'reason' => $data['reason'] ?? ($party === 'landlord' ? 'تعويض للمؤجر' : 'تعويض للمستأجر'),
            'status' => ($request->boolean('mark_paid') ? 'paid' : 'pending'),
            'paid_at' => ($request->boolean('mark_paid') ? now() : null),
        ]);

        if ($request->boolean('mark_paid') && $amount > 0 && $receiver) {
            // Credit receiver wallet
            $recvWallet = \App\Models\Wallet::firstOrCreate(
                ['user_id' => $receiver->id],
                ['balance' => 0, 'points_balance' => 0]
            );
            $credit = new \App\Models\Transaction;
            $credit->user_id = $recvWallet->user_id;
            $credit->wallet_id = $recvWallet->id;
            $credit->amount = $amount;
            $credit->type = 'credit';
            $credit->booking_id = $booking->id;
            $credit->meta = [
                'reason' => 'تعويض',
                'compensation_method' => $method,
            ];
            $credit->save();
            $recvWallet->balance = ($recvWallet->balance ?? 0) + $amount;
            $recvWallet->save();

            // Deduct from payer wallet when applicable
            if ($payer && $method !== 'fixed_extra') {
                $payWallet = \App\Models\Wallet::firstOrCreate(
                    ['user_id' => $payer->id],
                    ['balance' => 0, 'points_balance' => 0]
                );
                $debit = new \App\Models\Transaction;
                $debit->user_id = $payWallet->user_id;
                $debit->wallet_id = $payWallet->id;
                $debit->amount = -$amount;
                $debit->type = 'payment';
                $debit->booking_id = $booking->id;
                $debit->meta = [
                    'reason' => 'خصم مقابل تعويض',
                    'compensation_method' => $method,
                ];
                $debit->save();
                $payWallet->balance = ($payWallet->balance ?? 0) - $amount;
                $payWallet->save();
            }
        }

        // If there is a cancellation penalty for the payer, mark as paid optionally
        if ($request->boolean('mark_paid') && $payer) {
            $cancellationPenalty = Penalty::where('booking_id', $booking->id)
                ->where('user_id', $payer->id)
                ->where('type', 'cancellation')
                ->where('status', 'pending')
                ->first();
            if ($cancellationPenalty) {
                $cancellationPenalty->update(['status' => 'paid', 'paid_at' => now()]);
            }
        }

        return response()->json([
            'success' => true,
            'penalty' => $pen,
        ], 201);
    }

    public function apiPreviewCancellationPenalty(Request $request)
    {
        $bookingId = (int) $request->query('booking_id');
        if (! $bookingId) {
            return response()->json(['success' => false, 'message' => 'booking_id مطلوب'], 422);
        }

        $booking = \App\Models\Booking::with('property')->findOrFail($bookingId);
        $dailyPrice = (float) ($booking->property?->price ?? 0);
        $days = \Carbon\Carbon::parse($booking->start_date)->diffInDays(\Carbon\Carbon::parse($booking->end_date));
        $days = max(1, $days);
        $total = round($dailyPrice * $days, 2);

        $method = Setting::get('cancel_penalty_method', 'fixed');
        $percent = (float) (Setting::get('cancel_penalty_percentage', 0) ?: 0);
        $fixed = (float) (Setting::get('cancel_penalty_fixed_value', 0) ?: 0);

        $amount = $method === 'percentage' ? round($total * ($percent / 100), 2) : round($fixed, 2);
        $amount = max(0, min($amount, $total));

        return response()->json([
            'success' => true,
            'data' => [
                'method' => $method,
                'percentage' => $percent,
                'fixed_value' => $fixed,
                'booking_total' => $total,
                'penalty_amount' => $amount,
            ],
        ]);
    }
}
