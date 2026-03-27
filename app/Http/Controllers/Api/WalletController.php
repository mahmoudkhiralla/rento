<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\WalletResource;
use App\Models\PointsTransaction;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $wallet = Wallet::firstOrCreate(['user_id' => Auth::id()], [
            'balance' => 0,
        ]);

        return new WalletResource($wallet);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $wallet = Wallet::firstOrCreate(['user_id' => Auth::id()], [
            'balance' => 0,
        ]);

        $wallet->balance += $data['amount'];
        $wallet->save();

        Transaction::create([
            'user_id' => $wallet->user_id,
            'wallet_id' => $wallet->id,
            'amount' => $data['amount'],
            'type' => 'deposit',
        ]);

        return new WalletResource($wallet);
    }

    /**
     * Create a new wallet transaction (deposit, withdraw, payment, gift, credit).
     */
    public function addTransaction(Request $request)
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric'], // allow positive and negative
            'type' => ['required', 'string', 'in:deposit,withdraw,payment,gift,credit'],
            'booking_id' => ['nullable', 'exists:bookings,id'],
            'meta' => ['nullable'], // JSON object or string
        ]);

        $wallet = Wallet::firstOrCreate(['user_id' => Auth::id()], [
            'balance' => 0,
        ]);

        $transaction = new Transaction;
        $transaction->user_id = $wallet->user_id;
        $transaction->wallet_id = $wallet->id;
        $transaction->amount = $data['amount'];
        $transaction->type = $data['type'];
        $transaction->booking_id = $data['booking_id'] ?? null;
        if (array_key_exists('meta', $data) && ! is_null($data['meta'])) {
            $transaction->meta = is_array($data['meta']) ? json_encode($data['meta']) : $data['meta'];
        }
        $transaction->save();

        // Keep wallet balance consistent (while UI sums transactions as source of truth)
        $wallet->balance = ($wallet->balance ?? 0) + $data['amount'];
        $wallet->save();

        return response()->json([
            'message' => 'Transaction created',
            'wallet' => new WalletResource($wallet),
            'transaction' => $transaction,
        ], 201);
    }

    public function awardReferralPoints(Request $request)
    {
        $validated = $request->validate([
            'referrer_id' => 'required|exists:users,id',
            'referred_user_id' => 'nullable|exists:users,id',
        ]);

        $enabled = \App\Models\Setting::get('points_enabled', true);
        if (! $enabled) {
            return response()->json(['success' => false, 'message' => 'برنامج النقاط غير مُفعل'], 422);
        }

        $wallet = Wallet::firstOrCreate(['user_id' => $validated['referrer_id']], ['balance' => 0, 'points_balance' => 0]);

        $reasonKey = 'Referral';
        if (! empty($validated['referred_user_id'])) {
            $reasonKey = 'Referral:'.$validated['referred_user_id'];
            $exists = PointsTransaction::where('wallet_id', $wallet->id)
                ->where('type', 'earn')
                ->where('reason', $reasonKey)
                ->exists();
            if ($exists) {
                return response()->json(['success' => true, 'wallet' => $wallet, 'message' => 'already_awarded']);
            }
        }

        $points = (int) \App\Models\Setting::get('points_per_transaction', 100);
        $wallet->points_balance = ($wallet->points_balance ?? 0) + $points;
        $wallet->save();

        PointsTransaction::create([
            'wallet_id' => $wallet->id,
            'points' => $points,
            'type' => 'earn',
            'reason' => $reasonKey,
        ]);

        return response()->json(['success' => true, 'wallet' => $wallet]);
    }

    public function convertPoints(Request $request)
    {
        $validated = $request->validate([
            'amount_dinar' => 'required|numeric|min:0.01',
        ]);

        $user = $request->user();
        $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0, 'points_balance' => 0]);

        $enabled = \App\Models\Setting::get('points_enabled', true);
        if (! $enabled) {
            return response()->json(['success' => false, 'message' => 'برنامج النقاط غير مُفعل'], 422);
        }

        $pointsPerDinar = (int) \App\Models\Setting::get('points_per_dinar', 100);
        $minDinar = (float) \App\Models\Setting::get('min_points_conversion', 5);

        $amount = (float) $validated['amount_dinar'];
        if ($amount < $minDinar) {
            return response()->json(['success' => false, 'message' => 'أقل قيمة تحويل هي '.$minDinar.' دينار'], 422);
        }

        $requiredPoints = (int) ceil($amount * $pointsPerDinar);
        if (($wallet->points_balance ?? 0) < $requiredPoints) {
            return response()->json(['success' => false, 'message' => 'عدد النقاط غير كافٍ للتحويل'], 422);
        }

        // Deduct points
        $wallet->points_balance -= $requiredPoints;
        $wallet->balance = ($wallet->balance ?? 0) + $amount;
        $wallet->save();

        PointsTransaction::create([
            'wallet_id' => $wallet->id,
            'points' => $requiredPoints,
            'type' => 'redeem',
            'reason' => 'Points to money',
        ]);

        $transaction = new Transaction;
        $transaction->user_id = $wallet->user_id;
        $transaction->wallet_id = $wallet->id;
        $transaction->amount = $amount;
        $transaction->type = 'deposit';
        $transaction->status = 'completed';
        $transaction->meta = json_encode([
            'reason' => 'تحويل نقاط إلى رصيد',
            'points_spent' => $requiredPoints,
            'points_per_dinar' => $pointsPerDinar,
            'min_points_conversion_dinar' => $minDinar,
        ]);
        $transaction->save();

        return response()->json(['success' => true, 'wallet' => $wallet, 'transaction' => $transaction]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $wallet = Wallet::firstOrCreate(['user_id' => Auth::id()], [
            'balance' => 0,
        ]);

        return new WalletResource($wallet);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
