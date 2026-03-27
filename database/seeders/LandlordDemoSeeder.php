<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\PointsTransaction;
use App\Models\Property;
use App\Models\Review;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class LandlordDemoSeeder extends Seeder
{
    /**
     * Seed demo landlord user with properties and reviews for visual testing.
     */
    public function run(): void
    {
        // Use existing user with ID=1 if present, else create a new landlord
        $user = User::find(1);
        if (! $user) {
            $user = User::updateOrCreate(
                ['email' => 'landlord1@example.com'],
                [
                    'name' => 'منيرة أحمد',
                    'password' => Hash::make('password'),
                ]
            );
        }

        // Ensure landlord type and profile fields
        $user->fill([
            'user_type' => 'landlord',
            'job' => 'البرمجيات',
            'city' => 'طرابلس',
            'id_verified' => true,
            'face_verified' => true,
            'is_influencer' => false,
            'needs_renewal' => false,
            'status' => 'active',
            'has_pet' => false,
        ])->save();

        // Create 4 approved properties for the landlord
        $props = [
            ['title' => 'شقة فاخرة بالقرب من وسط المدينة', 'city' => 'طرابلس', 'price' => 20.00, 'description' => 'شقة نظيفة ومُرتبة وقريبة من جميع الخدمات.', 'approved' => true],
            ['title' => 'استوديو حديث بإطلالة جميلة', 'city' => 'بنغازي', 'price' => 25.00, 'description' => 'استوديو مناسب للإقامة القصيرة، مجهز بالكامل.', 'approved' => true],
            ['title' => 'منزل عائلي واسع', 'city' => 'مصراتة', 'price' => 30.00, 'description' => 'منزل مناسب للعائلات الكبيرة مع مساحة خارجية.', 'approved' => true],
            ['title' => 'سكن عملي لرحلات العمل', 'city' => 'سبها', 'price' => 18.00, 'description' => 'مناسب لرحلات العمل القصيرة.', 'approved' => true],
        ];

        $createdProps = [];
        foreach ($props as $p) {
            $createdProps[] = Property::updateOrCreate(
                ['user_id' => $user->id, 'title' => $p['title']],
                $p + ['user_id' => $user->id]
            );
        }

        // Create a demo tenant reviewer
        $tenant = User::updateOrCreate(
            ['email' => 'tenant1@example.com'],
            [
                'name' => 'مستأجر تجريبي',
                'password' => Hash::make('password'),
                'user_type' => 'tenant',
                'city' => 'طرابلس',
            ]
        );

        // Additional demo tenant for fake operations preview
        $tenant2 = User::updateOrCreate(
            ['email' => 'tenant2@example.com'],
            [
                'name' => 'محمود مرسي',
                'password' => Hash::make('password'),
                'user_type' => 'tenant',
                'city' => 'طرابلس',
            ]
        );

        // Create bookings and reviews to produce dynamic counts and rating
        $dates = [
            ['start' => '2025-01-20', 'end' => '2025-02-05'],
            ['start' => '2025-03-10', 'end' => '2025-03-15'],
            ['start' => '2025-04-01', 'end' => '2025-04-07'],
            ['start' => '2025-05-12', 'end' => '2025-05-15'],
        ];

        $bookings = [];
        foreach ($createdProps as $i => $property) {
            $booking = Booking::updateOrCreate(
                [
                    'user_id' => $tenant->id,
                    'property_id' => $property->id,
                    'start_date' => $dates[$i]['start'],
                    'end_date' => $dates[$i]['end'],
                ],
                [
                    'guests' => 2,
                    'status' => 'completed',
                ]
            );
            $bookings[] = $booking;

            Review::updateOrCreate(
                [
                    'reviewed_user_id' => $user->id,
                    'reviewer_user_id' => $tenant->id,
                    'booking_id' => $booking->id,
                ],
                [
                    'rating' => 4.9,
                    'property_care' => 4.8,
                    'cleanliness' => 4.7,
                    'rules_compliance' => 4.9,
                    'timely_delivery' => 5.0,
                    'comment' => 'تجربة ممتازة وتعامل راقٍ جدًا.',
                    'start_date' => $booking->start_date,
                    'end_date' => $booking->end_date,
                ]
            );
        }

        // Ensure wallet with balance and points for landlord
        $wallet = Wallet::updateOrCreate(
            ['user_id' => $user->id],
            [
                'balance' => 2375.00,
                'points_balance' => 25,
            ]
        );

        // Seed one booking-linked wallet transaction (net to landlord after commission)
        if (! empty($bookings)) {
            $sampleBooking = $bookings[0];
            $days = Carbon::parse($sampleBooking->start_date)->diffInDays(Carbon::parse($sampleBooking->end_date));
            $dailyPrice = $sampleBooking->property->price ?? 0;
            $total = round($dailyPrice * max(1, $days), 2);
            $commissionRate = 0.09; // 9% منصة
            $commission = round($total * $commissionRate, 2);
            $netToWallet = round($total - $commission, 2);

            Transaction::create([
                'wallet_id' => $wallet->id,
                'booking_id' => $sampleBooking->id,
                'amount' => $netToWallet,
                'type' => 'deposit',
                'meta' => json_encode([
                    'days' => $days,
                    'daily_price' => $dailyPrice,
                    'commission_rate' => $commissionRate,
                    'commission' => $commission,
                    'total' => $total,
                ]),
                'created_at' => Carbon::parse('2025-03-27 14:35:00'),
                'updated_at' => Carbon::parse('2025-03-27 14:35:00'),
            ]);
        }

        // Seed an additional fake booking and transaction to preview UI (5 days, custom totals)
        if (! empty($createdProps)) {
            $demoProp = $createdProps[0];
            $booking2 = Booking::updateOrCreate(
                [
                    'user_id' => $tenant2->id,
                    'property_id' => $demoProp->id,
                    'start_date' => '2025-03-22',
                    'end_date' => '2025-03-27',
                ],
                [
                    'guests' => 1,
                    'status' => 'completed',
                ]
            );

            $fakeDays = 5;
            $fakeRoomCharge = 2585.00; // إجمالي قيمة الحجز (قيمة تجريبية)
            $fakeCommissionRate = 0.09;
            $fakeCommission = round($fakeRoomCharge * $fakeCommissionRate);
            $fakeNet = 2350.00; // صافي المبلغ المضاف للمحفظة (قيمة تجريبية)

            Transaction::create([
                'wallet_id' => $wallet->id,
                'booking_id' => $booking2->id,
                'amount' => $fakeNet,
                'type' => 'deposit',
                'meta' => json_encode([
                    'days' => $fakeDays,
                    'daily_price' => 517.00, // قيمة يومية تجريبية
                    'commission_rate' => $fakeCommissionRate,
                    'commission' => $fakeCommission,
                    'total' => $fakeRoomCharge,
                    'room_charge' => $fakeRoomCharge,
                ]),
                'created_at' => Carbon::parse('2025-03-27 14:35:00'),
                'updated_at' => Carbon::parse('2025-03-27 14:35:00'),
            ]);
        }

        // Seed a withdraw transaction for visual testing
        Transaction::create([
            'wallet_id' => $wallet->id,
            'amount' => 125.00,
            'type' => 'withdraw',
            'meta' => json_encode(['note' => 'طلب سحب تم الموافقة عليه']),
        ]);

        // Additional points reward for preview
        PointsTransaction::create([
            'wallet_id' => $wallet->id,
            'points' => 25,
            'type' => 'earn',
            'reason' => 'من مكافآت النقاط',
            'created_at' => Carbon::parse('2025-03-27 14:40:00'),
            'updated_at' => Carbon::parse('2025-03-27 14:40:00'),
        ]);

        // Seed points rewards ledger
        PointsTransaction::updateOrCreate(
            ['wallet_id' => $wallet->id, 'points' => 5, 'type' => 'earn'],
            ['wallet_id' => $wallet->id, 'points' => 5, 'type' => 'earn', 'reason' => 'حجز مكتمل']
        );
        PointsTransaction::updateOrCreate(
            ['wallet_id' => $wallet->id, 'points' => 10, 'type' => 'earn'],
            ['wallet_id' => $wallet->id, 'points' => 10, 'type' => 'earn', 'reason' => 'تقييم إيجابي']
        );
        PointsTransaction::updateOrCreate(
            ['wallet_id' => $wallet->id, 'points' => 10, 'type' => 'earn'],
            ['wallet_id' => $wallet->id, 'points' => 10, 'type' => 'earn', 'reason' => 'عرض ترويجي']
        );
    }
}
