<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Transaction;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $allowedStatuses = ['confirmed', 'pending', 'canceled', 'paid', 'completed'];

        $query = Booking::where('user_id', Auth::id());

        // Optional status filter: ?status=confirmed or ?status[]=pending&status[]=paid
        $statusParam = request()->input('status');
        if (is_array($statusParam)) {
            $statuses = array_values(array_intersect($statusParam, $allowedStatuses));
            if (! empty($statuses)) {
                $query->whereIn('status', $statuses);
            }
        } elseif (is_string($statusParam) && in_array($statusParam, $allowedStatuses, true)) {
            $query->where('status', $statusParam);
        }

        $bookings = $query->latest()->paginate(10);

        return BookingResource::collection($bookings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'guests' => 'nullable|integer|min:1',
        ]);

        $data['user_id'] = Auth::id();

        $booking = Booking::create($data);

        return new BookingResource($booking);
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        // المستأجر يرى حجوزته، والمؤجر يرى حجوزات عقاراته، والإدمن يرى كل شيء
        $user = Auth::user();
        $isAdmin = method_exists($user, 'hasRole') && $user->hasRole('admin');
        $isTenantOwner = $booking->user_id === $user->id;
        $isLandlordOwner = ($booking->property?->user_id ?? null) === $user->id;

        if (! ($isAdmin || $isTenantOwner || $isLandlordOwner)) {
            abort(403, 'غير مصرح لك بعرض هذا الحجز');
        }

        return new BookingResource($booking->load(['user', 'property.user']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return response()->json(['message' => 'استخدم مسار تحديث الحالة PATCH /bookings/{id}/status'], 405);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * List bookings for landlord (properties owned by current user)
     */
    public function landlordIndex(Request $request)
    {
        $user = Auth::user();
        $isAdmin = method_exists($user, 'hasRole') && $user->hasRole('admin');
        $isLandlordRole = method_exists($user, 'hasRole') && $user->hasRole('landlord');
        $isLandlordType = in_array(($user->user_type ?? null), ['landlord', 'both']);

        if (! ($isAdmin || $isLandlordRole || $isLandlordType)) {
            abort(403, 'يتطلب صلاحيات مؤجر');
        }

        $allowedStatuses = ['confirmed', 'pending', 'review', 'canceled', 'paid', 'completed'];

        $query = Booking::with(['user', 'property'])
            ->whereHas('property', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->latest();

        // Optional status filter: ?status=confirmed or ?status[]=pending&status[]=paid
        $statusParam = $request->input('status');
        if (is_array($statusParam)) {
            $statuses = array_values(array_intersect($statusParam, $allowedStatuses));
            if (! empty($statuses)) {
                $query->whereIn('status', $statuses);
            }
        } elseif (is_string($statusParam) && in_array($statusParam, $allowedStatuses, true)) {
            $query->where('status', $statusParam);
        }

        return BookingResource::collection($query->paginate(10));
    }

    /**
     * Update booking status by landlord/admin
     */
    public function updateStatus(Request $request, Booking $booking)
    {
        $user = Auth::user();
        $isAdmin = method_exists($user, 'hasRole') && $user->hasRole('admin');
        $isLandlordOwner = ($booking->property?->user_id ?? null) === $user->id;
        if (! ($isAdmin || $isLandlordOwner)) {
            abort(403, 'غير مصرح لك بتعديل حالة هذا الحجز');
        }

        $data = $request->validate([
            'status' => 'required|in:confirmed,pending,review,canceled,paid,completed',
        ]);

        $booking->status = $data['status'];
        $booking->save();

        if ($data['status'] === 'confirmed') {
            try {
                $property = $booking->property;
                $landlordId = $property?->user_id;
                $dailyPrice = (float) ($property?->price ?? 0);
                $days = Carbon::parse($booking->start_date)->diffInDays(Carbon::parse($booking->end_date));
                $days = max(1, $days);

                if ($landlordId && $dailyPrice > 0) {
                    $total = round($dailyPrice * $days, 2);

                    $wallet = Wallet::firstOrCreate(
                        ['user_id' => $landlordId],
                        ['balance' => 0, 'points_balance' => 0]
                    );

                    $exists = Transaction::where('booking_id', $booking->id)
                        ->whereIn('type', ['credit', 'deposit'])
                        ->exists();
                    if (! $exists) {
                        $method = \App\Models\Setting::get('commission_calculation_method', 'percentage');
                        $percent = (float) \App\Models\Setting::get('commission_percentage', 0);
                        $fixed = (float) \App\Models\Setting::get('commission_fixed_value', 0);
                        $commission = $method === 'percentage' ? round($total * ($percent / 100), 2) : round($fixed, 2);
                        $commission = max(0, min($commission, $total));
                        $net = round($total - $commission, 2);

                        $transaction = new Transaction;
                        $transaction->user_id = $wallet->user_id;
                        $transaction->wallet_id = $wallet->id;
                        $transaction->amount = $net;
                        $transaction->type = 'payment';
                        $transaction->booking_id = $booking->id;
                        $transaction->meta = json_encode([
                            'reason' => 'تحصيل حجز مؤكد',
                            'daily_price' => $dailyPrice,
                            'days' => $days,
                            'total' => $total,
                            'commission_method' => $method,
                            'commission_rate' => $method === 'percentage' ? ($percent / 100) : null,
                            'commission_value' => $method === 'fixed' ? $fixed : null,
                            'commission' => $commission,
                            'net_to_wallet' => $net,
                            'property_id' => $property->id,
                            'tenant_id' => $booking->user_id,
                        ]);
                        $transaction->save();

                        $wallet->balance = ($wallet->balance ?? 0) + $net;
                        $wallet->save();
                    }
                }
            } catch (\Throwable $e) {
            }
        }

        return new BookingResource($booking->fresh(['user', 'property.user']));
    }
}
