<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentCard;
use Illuminate\Http\Request;

class PaymentCardsController extends Controller
{
    public function index(Request $request)
    {
        // Get filter parameters
        $status = $request->get('status');
        $search = $request->get('search');

        // Query payment cards
        $query = PaymentCard::with('user');

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('card_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $cards = $query->latest()->paginate(10);

        // Get statistics
        $stats = $this->getStatistics();

        return view('dashboard.payments.cards', compact('cards', 'stats'));
    }

    private function getStatistics()
    {
        return [
            'total_cards' => PaymentCard::count() ?: 0,
            'active_cards' => PaymentCard::where('status', 'active')->count() ?: 0,
            'pending_cards' => PaymentCard::where('status', 'pending')->count() ?: 0,
            'expired_cards' => PaymentCard::where('status', 'expired')->count() ?: 0,
            'cancelled_cards' => PaymentCard::where('status', 'cancelled')->count() ?: 0,
            'sold_cards' => PaymentCard::whereNotNull('user_id')->count() ?: 0,
            'total_issued' => PaymentCard::sum('amount') ?: 0,
        ];
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'card_count' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:0',
            'export_format' => 'nullable|string|in:csv,excel,pdf',
        ]);

        $count = (int) $validated['card_count'];
        $amount = (float) $validated['amount'];

        // Defaults to keep UI unchanged
        $cardType = 'standard';
        $issueDate = now();
        $expiryDate = now()->addDays(30);
        $status = 'pending';

        for ($i = 0; $i < $count; $i++) {
            PaymentCard::create([
                'card_number' => $this->generateCardNumber(),
                'card_type' => $cardType,
                'amount' => $amount,
                'balance' => $amount,
                'issue_date' => $issueDate,
                'expiry_date' => $expiryDate,
                'status' => $status,
                'notes' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم إصدار بطاقات الدفع بنجاح',
        ]);
    }

    private function generateCardNumber()
    {
        // Generate 16-digit grouped number like 1234 - 5678 - 9012 - 3456
        $digits = '';
        for ($i = 0; $i < 16; $i++) {
            $digits .= random_int(0, 9);
        }

        return substr($digits, 0, 4).' - '.substr($digits, 4, 4).' - '.substr($digits, 8, 4).' - '.substr($digits, 12, 4);
    }

    public function show($id)
    {
        $card = PaymentCard::with('user')->findOrFail($id);

        return response()->json([
            'success' => true,
            'card' => $card,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $card = PaymentCard::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,active,expired,cancelled',
        ]);
        $newStatus = $validated['status'];
        $currentStatus = $card->status;

        // Business rules:
        // 1) إيقاف البطاقة (cancelled) مسموح فقط من حالة الإصدار (pending)
        //    حالات: مباع (مربوطة بمستخدم) أو مشحون (active) أو ملغي لا يمكن إيقافها
        if ($newStatus === 'cancelled') {
            $isSold = ! is_null($card->user_id); // مباع: مربوطة بمستخدم
            if ($currentStatus !== 'pending' || $isSold) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن إيقاف البطاقة في حالات: مباع / مشحون / ملغي',
                ], 422);
            }
        }

        // 2) البطاقات الملغية أو المنتهية لا يمكن إعادة تفعيلها (active أو pending)
        if (in_array($currentStatus, ['cancelled', 'expired']) && in_array($newStatus, ['active', 'pending'])) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن إعادة تفعيل البطاقة الملغية أو المنتهية',
            ], 422);
        }

        // لا تغيير
        if ($newStatus === $currentStatus) {
            return response()->json([
                'success' => true,
                'message' => 'لا يوجد تغيير في حالة البطاقة',
            ]);
        }

        $card->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة البطاقة بنجاح',
        ]);
    }

    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        $limit = $request->get('limit', 25);

        $cards = PaymentCard::with('user')->latest()->take($limit)->get();

        if ($format === 'csv') {
            return $this->exportCsv($cards);
        }

        return response()->json([
            'success' => false,
            'message' => 'صيغة التصدير غير مدعومة',
        ]);
    }

    private function exportCsv($cards)
    {
        $filename = 'payment_cards_'.date('Y-m-d').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($cards) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Add headers
            fputcsv($file, ['رقم البطاقة', 'النوع', 'المبلغ', 'الحالة', 'تاريخ الإصدار', 'تاريخ الانتهاء']);

            // Add data
            foreach ($cards as $card) {
                fputcsv($file, [
                    $card->card_number,
                    $card->card_type,
                    $card->amount,
                    $card->status,
                    $card->issue_date,
                    $card->expiry_date,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
