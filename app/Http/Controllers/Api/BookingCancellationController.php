<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingCancellationController extends Controller
{
    /**
     * Cancel booking by renter (tenant).
     */
    public function cancelByRenter(Request $request, Booking $booking)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if ((int) $booking->user_id !== (int) $user->id) {
            return response()->json(['message' => 'هذا الحجز لا يخصك كمستأجر'], 403);
        }

        if ($booking->status === 'canceled') {
            return response()->json(['message' => 'الحجز ملغي مسبقًا', 'booking' => $booking], 200);
        }

        $booking->status = 'canceled';
        $booking->canceled_by = 'renter';
        $booking->canceled_at = now();
        $reason = $request->input('reason', $request->input('cancel_reason'));
        if (! is_null($reason) && $reason !== '') {
            $booking->cancel_reason = (string) $reason;
        }
        $booking->save();

        try {
            $booking->load(['property', 'user.wallet']);
            $dailyPrice = (float) ($booking->property?->price ?? 0);
            $days = \Carbon\Carbon::parse($booking->start_date)->diffInDays(\Carbon\Carbon::parse($booking->end_date));
            $days = max(1, $days);
            $total = round($dailyPrice * $days, 2);

            $method = \App\Models\Setting::get('cancel_penalty_method', 'fixed');
            $percent = (float) (\App\Models\Setting::get('cancel_penalty_percentage', 0) ?: 0);
            $fixed = (float) (\App\Models\Setting::get('cancel_penalty_fixed_value', 0) ?: 0);

            $penAmount = $method === 'percentage' ? round($total * ($percent / 100), 2) : round($fixed, 2);
            $penAmount = max(0, min($penAmount, $total));

            if ($penAmount > 0) {
                \App\Models\Penalty::create([
                    'user_id' => $booking->user_id,
                    'booking_id' => $booking->id,
                    'amount' => $penAmount,
                    'type' => 'cancellation',
                    'reason' => $booking->cancel_reason ?: 'إلغاء حجز من المستأجر',
                    'status' => 'pending',
                ]);
            }
        } catch (\Throwable $e) {
            // ignore calculation errors
        }

        return response()->json([
            'message' => 'تم إلغاء الحجز بواسطة المستأجر',
            'booking' => $booking->fresh(['user', 'property.user']),
        ], 200);
    }

    /**
     * Cancel booking by owner (landlord).
     */
    public function cancelByOwner(Request $request, Booking $booking)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $booking->load('property');

        // يسمح للمؤجر أو من لديه دور المؤجر أو الأدمن
        $isLandlordRole = method_exists($user, 'hasRole') && $user->hasRole('landlord');
        $isAdmin = method_exists($user, 'hasRole') && $user->hasRole('admin');
        $isLandlordType = in_array(($user->user_type ?? null), ['landlord', 'both'], true);

        if (! ($isLandlordRole || $isAdmin || $isLandlordType)) {
            return response()->json(['message' => 'يتطلب صلاحيات مؤجر'], 403);
        }

        if ((int) ($booking->property?->user_id) !== (int) $user->id && ! $isAdmin) {
            return response()->json(['message' => 'هذا الحجز لا يخص عقاراتك'], 403);
        }

        if ($booking->status === 'canceled') {
            return response()->json(['message' => 'الحجز ملغي مسبقًا', 'booking' => $booking], 200);
        }

        $booking->status = 'canceled';
        $booking->canceled_by = 'owner';
        $booking->canceled_at = now();
        $reason = $request->input('reason', $request->input('cancel_reason'));
        if (! is_null($reason) && $reason !== '') {
            $booking->cancel_reason = (string) $reason;
        }
        $booking->save();

        return response()->json([
            'message' => 'تم إلغاء الحجز بواسطة المؤجر',
            'booking' => $booking->fresh(['user', 'property.user']),
        ], 200);
    }

    /**
     * List canceled bookings with search and filters.
     * Query params:
     * - cq: search query against renter/owner name or email
     * - cby: filter by who canceled (renter|owner|any)
     * - page, per_page: pagination controls (default per_page=10)
     */
    public function listCanceled(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $query = Booking::with([
            'user:id,name,email',
            'property:id,title,city,price,user_id',
            'property.user:id,name,email',
        ])
            ->where('status', 'canceled');

        // Scope results by role/type: admin sees all; landlord sees bookings of own properties; tenant sees own bookings
        $isAdmin = (method_exists($user, 'hasRole') && $user->hasRole('admin'));
        $isLandlordRole = (method_exists($user, 'hasRole') && $user->hasRole('landlord'));
        $isLandlordType = in_array(($user->user_type ?? null), ['landlord', 'both'], true);
        if (! $isAdmin) {
            if ($isLandlordRole || $isLandlordType) {
                $query->whereHas('property', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            } else {
                $query->where('user_id', $user->id);
            }
        }

        // Filter by who canceled
        $cby = strtolower((string) $request->query('cby', 'any'));
        if (in_array($cby, ['renter', 'owner'], true)) {
            $query->where('canceled_by', $cby);
        }

        // Search across renter and owner
        $cq = trim((string) $request->query('cq', ''));
        if ($cq !== '') {
            $query->where(function ($q) use ($cq) {
                $q->whereHas('user', function ($u) use ($cq) {
                    $u->where('name', 'like', "%{$cq}%")
                        ->orWhere('email', 'like', "%{$cq}%");
                })->orWhereHas('property.user', function ($owner) use ($cq) {
                    $owner->where('name', 'like', "%{$cq}%")
                        ->orWhere('email', 'like', "%{$cq}%");
                });
            });
        }

        $perPage = (int) $request->query('per_page', 10);
        $perPage = max(1, min($perPage, 50));
        $paginator = $query->orderByDesc('canceled_at')->paginate($perPage)->withQueryString();

        $data = $paginator->getCollection()->map(function (Booking $b) {
            return [
                'id' => $b->id,
                'status' => $b->status,
                'start_date' => $b->start_date,
                'end_date' => $b->end_date,
                'canceled_at' => $b->canceled_at ? $b->canceled_at->toDateTimeString() : null,
                'canceled_by' => $b->canceled_by,
                'cancel_reason' => $b->cancel_reason,
                'renter' => [
                    'id' => $b->user?->id,
                    'name' => $b->user?->name,
                    'email' => $b->user?->email,
                ],
                'owner' => [
                    'id' => $b->property?->user?->id,
                    'name' => $b->property?->user?->name,
                    'email' => $b->property?->user?->email,
                ],
                'property' => [
                    'id' => $b->property?->id,
                    'title' => $b->property?->title,
                    'city' => $b->property?->city,
                    'price' => $b->property?->price,
                ],
            ];
        })->values();

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }
}
