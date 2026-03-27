<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Property;
use App\Models\Refund;
use App\Models\Review;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * Seed demo users, properties, bookings, and reviews for the dashboard.
     */
    public function run(): void
    {
        // Demo users
        $tenant = User::updateOrCreate(
            ['email' => 'tenant@example.com'],
            [
                'name' => 'أسلام محي الدين',
                'password' => Hash::make('password'),
            ]
        );

        $landlord = User::updateOrCreate(
            ['email' => 'landlord@example.com'],
            [
                'name' => 'اسم المؤجر',
                'password' => Hash::make('password'),
            ]
        );

        // Wallets for users
        Wallet::updateOrCreate(['user_id' => $tenant->id], ['balance' => 150.00]);
        Wallet::updateOrCreate(['user_id' => $landlord->id], ['balance' => 650.00]);

        // Properties owned by landlord
        $properties = [
            [
                'title' => 'شقة فاخرة بالقرب من وسط المدينة',
                'city' => 'طرابلس',
                'price' => 20.00,
                'description' => 'شقة نظيفة ومُرتبة وقريبة من جميع الخدمات.',
                'approved' => true,
                'image' => null, // يمكن تعديلها لاحقًا لربط صور محلية
            ],
            [
                'title' => 'استوديو حديث بإطلالة جميلة',
                'city' => 'بنغازي',
                'price' => 25.00,
                'description' => 'استوديو مناسب للإقامة القصيرة، مجهز بالكامل.',
                'approved' => true,
                'image' => null,
            ],
            [
                'title' => 'منزل عائلي واسع',
                'city' => 'مصراتة',
                'price' => 30.00,
                'description' => 'منزل مناسب للعائلات الكبيرة مع مساحة خارجية.',
                'approved' => true,
                'image' => null,
            ],
        ];

        $createdProperties = [];
        foreach ($properties as $p) {
            $createdProperties[] = Property::updateOrCreate(
                [
                    'user_id' => $landlord->id,
                    'title' => $p['title'],
                ],
                $p + ['user_id' => $landlord->id]
            );
        }

        // Bookings for tenant
        $dates = [
            ['start' => '2025-01-20', 'end' => '2025-02-05'],
            ['start' => '2025-03-10', 'end' => '2025-03-15'],
            ['start' => '2025-04-01', 'end' => '2025-04-07'],
        ];

        $createdBookings = [];
        foreach ($createdProperties as $idx => $property) {
            $createdBookings[$idx] = Booking::updateOrCreate(
                [
                    'user_id' => $tenant->id,
                    'property_id' => $property->id,
                    'start_date' => $dates[$idx]['start'],
                    'end_date' => $dates[$idx]['end'],
                ],
                [
                    'guests' => 2,
                    'status' => 'completed',
                ]
            );
        }

        // Reviews written by landlord about tenant (linked to bookings)
        $reviewTexts = [
            'مستأجر ملتزم ونظيف، أوصى بالتعامل معه مرة أخرى.',
            'حافظ على نظافة المكان واحترم القواعد بشكل ممتاز.',
            'تم التسليم في الموعد وتعامل راقٍ جدًا.',
        ];

        foreach ($createdBookings as $idx => $booking) {
            Review::updateOrCreate(
                [
                    'reviewed_user_id' => $tenant->id,
                    'reviewer_user_id' => $landlord->id,
                    'booking_id' => $booking->id,
                ],
                [
                    'rating' => 5,
                    'property_care' => 4.8,
                    'cleanliness' => 4.8,
                    'rules_compliance' => 4.8,
                    'timely_delivery' => 4.9,
                    'comment' => $reviewTexts[$idx],
                    'start_date' => $booking->start_date,
                    'end_date' => $booking->end_date,
                ]
            );
        }

        // Demo refunds: new pending requests (top table)
        $pendingRefunds = [
            ['request_type' => 'bank',   'bank_name' => 'مصرف الجمهورية', 'account_number' => '9274639463-32084', 'account_type' => 'مستأجر', 'amount' => 150.00, 'status' => 'pending', 'account_holder' => 'Jane Cooper'],
            ['request_type' => 'wallet', 'bank_name' => 'G-Pay',          'account_number' => '+218 94 548 8765',  'account_type' => 'مستأجر', 'amount' => 150.00, 'status' => 'pending', 'account_holder' => 'Jane Cooper'],
            ['request_type' => 'wallet', 'bank_name' => 'المدار الجديد',  'account_number' => '+218 91 548 8765',  'account_type' => 'مستأجر', 'amount' => 150.00, 'status' => 'pending', 'account_holder' => 'Jane Cooper'],
        ];

        foreach ($pendingRefunds as $r) {
            Refund::updateOrCreate(
                ['user_id' => $tenant->id, 'account_number' => $r['account_number'], 'status' => 'pending'],
                $r + ['user_id' => $tenant->id]
            );
        }

        // Demo refunds: previous approved/rejected (bottom table)
        $previousRefunds = [
            ['request_type' => 'wallet', 'bank_name' => 'G-Pay',          'account_number' => '+218 94 548 8765',  'account_type' => 'مستأجر', 'amount' => 150.00, 'status' => 'approved', 'account_holder' => 'Jane Cooper', 'processed_at' => now()],
            ['request_type' => 'wallet', 'bank_name' => 'المدار الجديد',  'account_number' => '+218 91 548 8765',  'account_type' => 'مستأجر', 'amount' => 150.00, 'status' => 'rejected', 'account_holder' => 'Jane Cooper', 'processed_at' => now()],
            ['request_type' => 'bank',   'bank_name' => 'مصرف الصداري',   'account_number' => '9274639463-32084', 'account_type' => 'مستأجر', 'amount' => 150.00, 'status' => 'pending',  'account_holder' => 'Jane Cooper'],
            ['request_type' => 'bank',   'bank_name' => 'مصرف الامان',     'account_number' => '9274639463-32084', 'account_type' => 'مستأجر', 'amount' => 150.00, 'status' => 'approved', 'account_holder' => 'Jane Cooper', 'processed_at' => now()],
            ['request_type' => 'bank',   'bank_name' => 'مصرف الجمهورية', 'account_number' => '9274639463-32084', 'account_type' => 'مستأجر', 'amount' => 150.00, 'status' => 'approved', 'account_holder' => 'Jane Cooper', 'processed_at' => now()],
        ];

        foreach ($previousRefunds as $r) {
            Refund::updateOrCreate(
                ['user_id' => $tenant->id, 'account_number' => $r['account_number'], 'status' => $r['status']],
                $r + ['user_id' => $tenant->id]
            );
        }
    }
}
