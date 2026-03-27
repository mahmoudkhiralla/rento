<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use Illuminate\Http\Request;

class RefundsController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $type = $request->get('type');
        $search = $request->get('search');

        $newQuery = Refund::with(['user', 'user.wallet'])
            ->where('status', 'pending');

        if ($type && $type !== 'all') {
            $newQuery->where('request_type', $type);
        }

        if ($search) {
            $newQuery->where(function ($q) use ($search) {
                $q->where('account_number', 'like', "%{$search}%")
                    ->orWhere('bank_name', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $refunds = $newQuery->latest()->paginate(5);

        $prevQuery = Refund::with(['user', 'user.wallet'])
            ->when(($status && in_array($status, ['approved', 'rejected'])), function ($q) use ($status) {
                $q->where('status', $status);
            }, function ($q) {
                $q->where('status', '!=', 'pending');
            })
            ->when(($type && $type !== 'all'), function ($q) use ($type) {
                $q->where('request_type', $type);
            })
            ->when($search, function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('account_number', 'like', "%{$search}%")
                        ->orWhere('bank_name', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($uq) use ($search) {
                            $uq->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            });

        $refundsPrev = $prevQuery->latest()->paginate(5, ['*'], 'prev_page');

        // Get statistics
        $stats = $this->getStatistics();

        return view('dashboard.payments.refunds', compact('refunds', 'refundsPrev', 'stats'));
    }

    private function getStatistics()
    {
        return [
            'total_requests' => Refund::count() ?: 0,
            'pending_requests' => Refund::where('status', 'pending')->count() ?: 0,
            'approved_requests' => Refund::where('status', 'approved')->count() ?: 0,
            'rejected_requests' => Refund::where('status', 'rejected')->count() ?: 0,
            'total_amount' => Refund::where('status', 'approved')->sum('amount') ?: 0,
        ];
    }

    public function show($id)
    {
        $refund = Refund::with('user')->findOrFail($id);

        return response()->json([
            'success' => true,
            'refund' => $refund,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $refund = Refund::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'notes' => 'nullable|string',
        ]);

        $refund->update([
            'status' => $validated['status'],
            'admin_notes' => $validated['notes'] ?? null,
            'processed_at' => now(),
        ]);

        if (in_array($validated['status'], ['approved', 'rejected']) && $refund->user) {
            \App\Models\Notification::create([
                'user_id' => $refund->user_id,
                'title' => $validated['status'] === 'approved' ? 'تمت الموافقة على طلب السحب' : 'تم رفض طلب السحب',
                'message' => $validated['status'] === 'approved'
                    ? 'تمت الموافقة على طلب سحب الرصيد وسيتم تحويل المبلغ.'
                    : 'تم رفض طلب سحب الرصيد. يرجى مراجعة البيانات والمحاولة لاحقاً.',
                'type' => 'refund',
                'channel' => 'push',
                'target_users' => 'specific',
                'meta' => ['refund_id' => $refund->id, 'amount' => $refund->amount, 'status' => $validated['status']],
                'sent_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة الطلب بنجاح',
        ]);
    }
}
