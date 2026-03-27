<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penalty;
use App\Models\Refund;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class TransactionsController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $type = $request->get('type');

        $entries = collect();

        $txnQuery = Transaction::with(['user', 'wallet.user', 'booking']);
        if ($status && $status !== 'all') {
            $txnQuery->where('status', $status);
        }
        if ($type && in_array($type, ['deposit', 'withdraw', 'payment'])) {
            $txnQuery->where('type', $type);
        }
        $transactionRows = $txnQuery->latest()->get()->map(function ($t) {
            return (object) [
                'id' => 'txn-'.$t->id,
                'type' => $t->type,
                'status' => $t->status,
                'amount' => (float) $t->amount,
                'created_at' => $t->created_at,
                'user' => ($t->user ?: ($t->wallet ? $t->wallet->user : null)),
            ];
        });
        $entries = $entries->merge($transactionRows);

        $commissionRows = Transaction::with(['user', 'wallet.user', 'booking'])
            ->whereIn('type', ['payment', 'deposit', 'credit'])
            ->get()
            ->map(function ($t) {
                $commission = 0;
                $meta = $t->meta ?? [];
                if (is_array($meta)) {
                    $commission = (float) ($meta['commission'] ?? 0);
                } else {
                    $commission = (float) (data_get($meta, 'commission', 0));
                }
                if ($commission <= 0) {
                    return null;
                }
                $user = ($t->user ?: ($t->wallet ? $t->wallet->user : null));

                return (object) [
                    'id' => 'commission-'.$t->id,
                    'type' => 'commission',
                    'status' => 'completed',
                    'amount' => $commission,
                    'created_at' => $t->created_at,
                    'user' => $user,
                ];
            })->filter();
        $entries = $entries->merge($commissionRows);

        $penQuery = Penalty::with(['user', 'booking']);
        if ($status && $status !== 'all') {
            $penStatus = $status === 'completed' ? 'paid' : ($status === 'cancelled' ? 'cancelled' : ($status === 'pending' ? 'pending' : null));
            if ($penStatus) {
                $penQuery->where('status', $penStatus);
            }
        }
        $penaltyRows = $penQuery->latest()->get()->map(function ($p) {
            $status = $p->status === 'paid' ? 'completed' : ($p->status === 'cancelled' ? 'cancelled' : 'pending');

            return (object) [
                'id' => 'penalty-'.$p->id,
                'type' => 'penalty',
                'status' => $status,
                'amount' => (float) $p->amount,
                'created_at' => $p->created_at,
                'user' => $p->user,
            ];
        });
        $entries = $entries->merge($penaltyRows);

        $refQuery = Refund::with(['user']);
        if ($status && $status !== 'all') {
            $refStatus = $status === 'completed' ? 'approved' : ($status === 'failed' ? 'rejected' : ($status === 'pending' ? 'pending' : null));
            if ($refStatus) {
                $refQuery->where('status', $refStatus);
            }
        }
        $refundRows = $refQuery->latest()->get()->map(function ($r) {
            return (object) [
                'id' => 'refund-'.$r->id,
                'type' => 'refund',
                'status' => $r->status === 'approved' ? 'completed' : ($r->status === 'rejected' ? 'failed' : 'pending'),
                'amount' => (float) $r->amount,
                'created_at' => $r->created_at,
                'user' => $r->user,
            ];
        });
        $entries = $entries->merge($refundRows);

        if ($type && $type !== 'all') {
            $entries = $entries->where('type', $type);
        }

        if ($status && $status !== 'all') {
            $entries = $entries->where('status', $status);
        }

        $sorted = $entries->sortByDesc(function ($e) {
            return $e->created_at instanceof \Illuminate\Support\Carbon ? $e->created_at->timestamp : strtotime($e->created_at);
        })->values();

        $page = (int) ($request->get('page', 1));
        $perPage = 10;
        $transactions = new LengthAwarePaginator(
            $sorted->forPage($page, $perPage),
            $sorted->count(),
            $perPage,
            $page,
            [
                'path' => url()->current(),
                'query' => $request->query(),
            ]
        );

        // Get statistics
        $stats = $this->getStatistics();

        return view('dashboard.payments.transactions', compact('transactions', 'stats'));
    }

    private function getStatistics()
    {
        $walletTotalBalance = Wallet::sum('balance');

        // Sum commissions only from booking payments, using stored meta values
        $totalCommissions = Transaction::where('type', 'payment')
            ->whereNotNull('booking_id')
            ->get(['id', 'meta'])
            ->sum(function ($t) {
                $meta = $t->meta ?? [];
                if (is_array($meta)) {
                    return (float) ($meta['commission'] ?? 0);
                }

                return (float) (data_get($meta, 'commission', 0));
            });

        $totalRefundsAmount = Refund::where('status', 'approved')->sum('amount');
        $pendingRefundsCount = Refund::where('status', 'pending')->count();

        return [
            'wallet_total_balance' => $walletTotalBalance,
            'total_commissions' => $totalCommissions,
            'total_refunds_amount' => $totalRefundsAmount,
            'pending_refunds_count' => $pendingRefundsCount,
        ];
    }

    public function show($id)
    {
        $transaction = Transaction::with(['user.wallet', 'booking'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'transaction' => $transaction,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,completed,failed,cancelled',
        ]);

        $transaction->update([
            'status' => $validated['status'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة العملية بنجاح',
        ]);
    }
}
