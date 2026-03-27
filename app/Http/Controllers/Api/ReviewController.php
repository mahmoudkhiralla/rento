<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'reviewed_user_id' => ['required', 'exists:users,id'],
            'booking_id' => ['nullable', 'exists:bookings,id'],
            'rating' => ['required', 'numeric', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string'],
            'inquiry_response' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'booking_acceptance_speed' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'timely_delivery' => ['nullable', 'numeric', 'min:0', 'max:5'],
        ]);

        $reviewer = Auth::user();
        if (! $reviewer) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if (! in_array($reviewer->user_type, ['landlord', 'both'])) {
            return response()->json(['message' => 'فقط المؤجر يمكنه إضافة مراجعة للمستأجر'], 403);
        }
        $booking = null;
        if (! empty($data['booking_id'])) {
            $booking = Booking::with('property')->find($data['booking_id']);
            if (! $booking) {
                return response()->json(['message' => 'الحجز غير موجود'], 404);
            }
            if ((int) ($booking->property?->user_id) !== (int) $reviewer->id) {
                return response()->json(['message' => 'هذا الحجز لا يخص عقاراتك'], 422);
            }
            if ((int) $booking->user_id !== (int) $data['reviewed_user_id']) {
                return response()->json(['message' => 'المستخدم المستهدف لا يطابق حجز الطلب'], 422);
            }
            $exists = Review::where('reviewed_user_id', (int) $data['reviewed_user_id'])
                ->where('reviewer_user_id', $reviewer->id)
                ->where('booking_id', $data['booking_id'])
                ->exists();
            if ($exists) {
                return response()->json(['message' => 'تم إضافة مراجعة مسبقًا لهذا الحجز والمستأجر'], 422);
            }
        }

        $target = User::find($data['reviewed_user_id']);
        if (! $target || ! in_array(($target->user_type ?? ''), ['tenant', 'both'])) {
            return response()->json(['message' => 'يمكن مراجعة المستأجر فقط عبر هذا المسار'], 422);
        }

        $review = Review::create([
            'reviewed_user_id' => (int) $data['reviewed_user_id'],
            'reviewer_user_id' => $reviewer->id,
            'booking_id' => $data['booking_id'] ?? null,
            'rating' => $data['rating'],
            'inquiry_response' => $data['inquiry_response'] ?? null,
            'booking_acceptance_speed' => $data['booking_acceptance_speed'] ?? null,
            'timely_delivery' => $data['timely_delivery'] ?? null,
            'comment' => $data['comment'] ?? null,
            'start_date' => $booking?->start_date,
            'end_date' => $booking?->end_date,
        ]);

        return response()->json([
            'message' => 'تم إنشاء المراجعة بنجاح',
            'review' => $review,
        ], 201);
    }

    public function storeLandlordReview(Request $request)
    {
        $data = $request->validate([
            'reviewed_user_id' => ['required', 'exists:users,id'],
            'booking_id' => ['nullable', 'exists:bookings,id'],
            'rating' => ['required', 'numeric', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string'],
            'property_care' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'cleanliness' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'rules_compliance' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'timely_delivery' => ['nullable', 'numeric', 'min:0', 'max:5'],
        ]);

        $reviewer = Auth::user();
        if (! $reviewer) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if (! in_array($reviewer->user_type, ['tenant', 'both'])) {
            return response()->json(['message' => 'فقط المستأجر يمكنه إضافة مراجعة للمؤجر'], 403);
        }
        $booking = null;
        if (! empty($data['booking_id'])) {
            $booking = Booking::with('property')->find($data['booking_id']);
            if (! $booking) {
                return response()->json(['message' => 'الحجز غير موجود'], 404);
            }
            if ((int) $booking->user_id !== (int) $reviewer->id) {
                return response()->json(['message' => 'هذا الحجز لا يخصك'], 422);
            }
            if ((int) ($booking->property?->user_id) !== (int) $data['reviewed_user_id']) {
                return response()->json(['message' => 'المستخدم المستهدف لا يطابق مالك العقار في الحجز'], 422);
            }
            $exists = Review::where('reviewed_user_id', (int) $data['reviewed_user_id'])
                ->where('reviewer_user_id', $reviewer->id)
                ->where('booking_id', $data['booking_id'])
                ->exists();
            if ($exists) {
                return response()->json(['message' => 'تم إضافة مراجعة مسبقًا لهذا الحجز والمؤجر'], 422);
            }
        }

        $target = User::find($data['reviewed_user_id']);
        if (! $target || ! in_array(($target->user_type ?? ''), ['landlord', 'both'])) {
            return response()->json(['message' => 'يمكن مراجعة المؤجر فقط عبر هذا المسار'], 422);
        }

        $review = Review::create([
            'reviewed_user_id' => (int) $data['reviewed_user_id'],
            'reviewer_user_id' => $reviewer->id,
            'booking_id' => $data['booking_id'] ?? null,
            'rating' => $data['rating'],
            'property_care' => $data['property_care'] ?? null,
            'cleanliness' => $data['cleanliness'] ?? null,
            'rules_compliance' => $data['rules_compliance'] ?? null,
            'timely_delivery' => $data['timely_delivery'] ?? null,
            'comment' => $data['comment'] ?? null,
            'start_date' => $booking?->start_date,
            'end_date' => $booking?->end_date,
        ]);

        return response()->json([
            'message' => 'تم إنشاء المراجعة بنجاح',
            'review' => $review,
        ], 201);
    }
}
