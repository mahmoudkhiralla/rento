<?php

namespace Database\Seeders;

use App\Models\ActivePlace;
use App\Models\Booking;
use App\Models\PointsTransaction;
use App\Models\Property;
use App\Models\Review;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RichUsersSeeder extends Seeder
{
    public function run(): void
    {
        // مؤجرون جدد
        $landlordsData = [
            [
                'name' => 'محمد المؤجر',
                'email' => 'landlord1@local.test',
                'avatar' => 'https://i.pravatar.cc/150?img=11',
                'city' => 'طرابلس',
                'job' => 'مقاول',
            ],
            [
                'name' => 'سالم المؤجر',
                'email' => 'landlord2@local.test',
                'avatar' => 'https://i.pravatar.cc/150?img=12',
                'city' => 'بنغازي',
                'job' => 'تاجر',
            ],
        ];

        $landlords = [];
        foreach ($landlordsData as $ld) {
            $landlords[] = User::updateOrCreate(
                ['email' => $ld['email']],
                array_merge($ld, [
                    'password' => Hash::make('password'),
                    'user_type' => 'landlord',
                    'id_verified' => true,
                    'face_verified' => true,
                    'status' => 'active',
                    'has_pet' => false,
                    'is_influencer' => false,
                    'needs_renewal' => false,
                ])
            );
        }

        // مستأجرون جدد
        $tenantsData = [
            [
                'name' => 'بلال المستأجر',
                'email' => 'tenant1@local.test',
                'avatar' => 'https://i.pravatar.cc/150?img=21',
                'city' => 'مصراتة',
                'job' => 'مهندس',
                'has_pet' => true,
            ],
            [
                'name' => 'رامي المستأجر',
                'email' => 'tenant2@local.test',
                'avatar' => 'https://i.pravatar.cc/150?img=22',
                'city' => 'سبها',
                'job' => 'محاسب',
                'has_pet' => false,
            ],
        ];

        $tenants = [];
        foreach ($tenantsData as $td) {
            $tenants[] = User::updateOrCreate(
                ['email' => $td['email']],
                array_merge($td, [
                    'password' => Hash::make('password'),
                    'user_type' => 'tenant',
                    'id_verified' => true,
                    'face_verified' => false,
                    'status' => 'active',
                    'is_influencer' => false,
                    'needs_renewal' => false,
                ])
            );
        }

        // محافظ ومعاملات ونقاط
        foreach (array_merge($landlords, $tenants) as $u) {
            $wallet = Wallet::updateOrCreate(
                ['user_id' => $u->id],
                ['balance' => rand(500, 2500), 'points_balance' => rand(100, 2000)]
            );

            // معاملات مالية (استخدم type=credit ليتوافق مع الواجهة)
            Transaction::updateOrCreate(
                ['wallet_id' => $wallet->id, 'amount' => 1000, 'type' => 'credit'],
                ['meta' => json_encode(['reason' => 'إيداع أولي']), 'user_id' => $wallet->user_id]
            );
            Transaction::updateOrCreate(
                ['wallet_id' => $wallet->id, 'amount' => -200, 'type' => 'payment'],
                ['meta' => json_encode(['reason' => 'دفع حجز']), 'user_id' => $wallet->user_id]
            );
            Transaction::updateOrCreate(
                ['wallet_id' => $wallet->id, 'amount' => 300, 'type' => 'credit'],
                ['meta' => json_encode(['reason' => 'مكافأة']), 'user_id' => $wallet->user_id]
            );

            // نقاط
            PointsTransaction::updateOrCreate(
                ['wallet_id' => $wallet->id, 'points' => 200, 'type' => 'earn'],
                ['reason' => 'نقاط ترحيب']
            );
            PointsTransaction::updateOrCreate(
                ['wallet_id' => $wallet->id, 'points' => -50, 'type' => 'spend'],
                ['reason' => 'استبدال نقاط']
            );
        }

        // عقارات للمؤجرين
        $unsplash = [
            'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=800&h=500&q=60',
            'https://images.unsplash.com/photo-1493809842364-78817add7ffb?auto=format&fit=crop&w=800&h=500&q=60',
            'https://images.unsplash.com/photo-1495197359483-d092478c170a?auto=format&fit=crop&w=800&h=500&q=60',
            'https://images.unsplash.com/photo-1560185127-6ed189bf02a8?auto=format&fit=crop&w=800&h=500&q=60',
        ];

        $allProperties = [];
        foreach ($landlords as $idx => $landlord) {
            $props = [
                [
                    'title' => 'شقة حديثة في '.($landlord->city ?? 'طرابلس'),
                    'city' => $landlord->city ?? 'طرابلس',
                    'price' => 25.00,
                    'description' => 'شقة مجهزة بالكامل وقريبة من الخدمات.',
                    'approved' => true,
                    'image' => $unsplash[$idx % count($unsplash)],
                ],
                [
                    'title' => 'استوديو مريح بإطلالة جميلة',
                    'city' => $landlord->city ?? 'طرابلس',
                    'price' => 18.50,
                    'description' => 'مناسب للإقامات القصيرة مع واي فاي سريع.',
                    'approved' => true,
                    'image' => $unsplash[($idx + 1) % count($unsplash)],
                ],
            ];

            foreach ($props as $p) {
                $allProperties[] = Property::updateOrCreate(
                    ['user_id' => $landlord->id, 'title' => $p['title']],
                    $p + ['user_id' => $landlord->id]
                );
            }
        }

        // حجوزات للمستأجرين وربط مراجعات للطرفين
        $datePairs = [
            ['start' => now()->subDays(40)->toDateString(), 'end' => now()->subDays(35)->toDateString()],
            ['start' => now()->subDays(20)->toDateString(), 'end' => now()->subDays(15)->toDateString()],
            ['start' => now()->subDays(10)->toDateString(), 'end' => now()->subDays(7)->toDateString()],
        ];

        foreach ($tenants as $tIdx => $tenant) {
            foreach ($allProperties as $pIdx => $property) {
                if ($pIdx % 2 !== $tIdx % 2) {
                    continue;
                } // تقليل عدد الحجوزات

                $dates = $datePairs[$pIdx % count($datePairs)];
                $booking = Booking::updateOrCreate(
                    [
                        'user_id' => $tenant->id,
                        'property_id' => $property->id,
                        'start_date' => $dates['start'],
                        'end_date' => $dates['end'],
                    ],
                    [
                        'guests' => rand(1, 4),
                        'status' => 'completed',
                    ]
                );

                // مراجعة من المؤجر للمستأجر
                Review::updateOrCreate(
                    [
                        'reviewed_user_id' => $tenant->id,
                        'reviewer_user_id' => $property->user_id,
                        'booking_id' => $booking->id,
                    ],
                    [
                        'rating' => rand(4, 5),
                        'property_care' => 4.7,
                        'cleanliness' => 4.6,
                        'rules_compliance' => 4.8,
                        'timely_delivery' => 4.9,
                        'comment' => 'مستأجر ملتزم ونظيف، تجربة ممتازة.',
                        'start_date' => $booking->start_date,
                        'end_date' => $booking->end_date,
                    ]
                );

                // مراجعة من المستأجر للمؤجر
                Review::updateOrCreate(
                    [
                        'reviewed_user_id' => $property->user_id,
                        'reviewer_user_id' => $tenant->id,
                        'booking_id' => $booking->id,
                    ],
                    [
                        'rating' => rand(4, 5),
                        'property_care' => 4.5,
                        'cleanliness' => 4.6,
                        'rules_compliance' => 4.7,
                        'timely_delivery' => 4.8,
                        'comment' => 'مؤجر متعاون والمكان مطابق للوصف.',
                        'start_date' => $booking->start_date,
                        'end_date' => $booking->end_date,
                    ]
                );

                // معاملات مرتبطة بالحجز
                $wallet = Wallet::firstOrCreate(['user_id' => $property->user_id], ['balance' => 0, 'points_balance' => 0]);
                Transaction::updateOrCreate(
                    [
                        'wallet_id' => $wallet->id,
                        'amount' => $property->price,
                        'type' => 'credit',
                        'booking_id' => $booking->id,
                    ],
                    ['meta' => json_encode(['reason' => 'تحصيل حجز', 'property_id' => $property->id]), 'user_id' => $wallet->user_id]
                );
            }
        }

        // ActivePlaces للمستخدمين الجدد
        foreach ($landlords as $landlord) {
            ActivePlace::updateOrCreate([
                'user_id' => $landlord->id,
                'name' => 'فيلا حديثة بإطلالة',
            ], [
                'user_id' => $landlord->id,
                'user_type' => 'landlord',
                'name' => 'فيلا حديثة بإطلالة',
                'city' => $landlord->city ?? 'طرابلس',
                'area' => 'حي الأندلس',
                'available_from' => now()->addDays(5)->toDateString(),
                'booking_type' => 'إيجار يومي',
                'price' => 30.00,
                'price_unit' => 'د.ل / اليوم',
                'rating' => 4.8,
                'image' => $unsplash[0],
            ]);

            ActivePlace::updateOrCreate([
                'user_id' => $landlord->id,
                'name' => 'شقة فاخرة قريبة من الخدمات',
            ], [
                'user_id' => $landlord->id,
                'user_type' => 'landlord',
                'name' => 'شقة فاخرة قريبة من الخدمات',
                'city' => $landlord->city ?? 'طرابلس',
                'area' => 'النوفليين',
                'available_from' => now()->addDays(10)->toDateString(),
                'booking_type' => 'إيجار ليلي',
                'price' => 45.00,
                'price_unit' => 'د.ل / ليلة',
                'rating' => 4.6,
                'image' => $unsplash[1],
            ]);
        }

        foreach ($tenants as $tenant) {
            ActivePlace::updateOrCreate([
                'user_id' => $tenant->id,
                'name' => 'آخر إقامة في '.($tenant->city ?? 'طرابلس'),
            ], [
                'user_id' => $tenant->id,
                'user_type' => 'tenant',
                'name' => 'آخر إقامة في '.($tenant->city ?? 'طرابلس'),
                'city' => $tenant->city ?? 'طرابلس',
                'area' => 'وسط المدينة',
                'available_from' => now()->subDays(20)->toDateString(),
                'booking_type' => 'إيجار قصير',
                'price' => 22.00,
                'price_unit' => 'د.ل / يوم',
                'rating' => 4.7,
                'image' => $unsplash[2],
            ]);
        }
    }
}
