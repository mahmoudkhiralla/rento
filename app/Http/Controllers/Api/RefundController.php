<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Refund;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RefundController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Refund::with('user')->where('user_id', $user->id);
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }
        if ($request->filled('type')) {
            $query->where('request_type', $request->string('type'));
        }
        $refunds = $query->latest()->paginate(10);

        return response()->json(['success' => true, 'data' => $refunds]);
    }

    public function show($id)
    {
        $user = Auth::user();
        $refund = Refund::with('user')->where('user_id', $user->id)->findOrFail($id);

        return response()->json(['success' => true, 'refund' => $refund]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'request_type' => ['required', 'in:bank,wallet,cash'],
            'bank_name' => ['nullable', 'string'],
            'account_number' => ['nullable', 'string'],
        ]);

        $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);
        if (($wallet->balance ?? 0) < $data['amount']) {
            return response()->json([
                'success' => false,
                'message' => 'الرصيد غير كافٍ لإجراء طلب السحب',
            ], 422);
        }

        $accountType = match ($user->user_type) {
            'tenant' => 'مستأجر',
            'landlord' => 'مؤجر',
            default => 'مستأجر',
        };

        $refund = Refund::create([
            'user_id' => $user->id,
            'request_type' => $data['request_type'],
            'amount' => $data['amount'],
            'account_type' => $accountType,
            'account_number' => $data['account_number'] ?? null,
            'bank_name' => $data['bank_name'] ?? null,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء طلب سحب الرصيد وتمت إضافته إلى الطلبات الجديدة',
            'refund' => $refund,
        ], 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();
        if (! $user || ! method_exists($user, 'hasRole') || ! $user->hasRole('admin')) {
            return response()->json(['success' => false, 'message' => 'غير مصرح'], 403);
        }
        $data = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'notes' => 'nullable|string',
        ]);
        $refund = Refund::findOrFail($id);
        $refund->update([
            'status' => $data['status'],
            'admin_notes' => $data['notes'] ?? null,
            'processed_at' => now(),
        ]);
        if (in_array($data['status'], ['approved', 'rejected']) && $refund->user_id) {
            Notification::create([
                'user_id' => $refund->user_id,
                'title' => $data['status'] === 'approved' ? 'تمت الموافقة على طلب السحب' : 'تم رفض طلب السحب',
                'message' => $data['status'] === 'approved' ? 'تمت الموافقة على طلب سحب الرصيد وسيتم تحويل المبلغ.' : 'تم رفض طلب سحب الرصيد. يرجى مراجعة البيانات والمحاولة لاحقاً.',
                'type' => 'refund',
                'channel' => 'push',
                'target_users' => 'specific',
                'meta' => ['refund_id' => $refund->id, 'amount' => $refund->amount, 'status' => $data['status']],
                'sent_at' => now(),
            ]);
        }

        return response()->json(['success' => true, 'message' => 'تم تحديث حالة الطلب']);
    }
}
