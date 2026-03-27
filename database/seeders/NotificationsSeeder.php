<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some users for testing
        $users = User::inRandomOrder()->limit(10)->get();

        if ($users->isEmpty()) {
            $this->command->warn('لا يوجد مستخدمين في قاعدة البيانات. يرجى إنشاء مستخدمين أولاً.');

            return;
        }

        $notificationsData = [
            [
                'type' => 'announcement',
                'channel' => 'sms',
                'target_users' => 'all',
                'title' => 'عرض خاص - خصم 20% على جميع الحجوزات',
                'message' => 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...',
            ],
            [
                'type' => 'alert',
                'channel' => 'push',
                'target_users' => 'tenants',
                'title' => 'تأكيد حجز جديد',
                'message' => 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...',
            ],
            [
                'type' => 'info',
                'channel' => 'email',
                'target_users' => 'landlords',
                'title' => 'إعلان عن صيانة دورية',
                'message' => 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...',
            ],
            [
                'type' => 'announcement',
                'channel' => 'push',
                'target_users' => 'specific',
                'title' => 'إلغاء حجز',
                'message' => 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...',
            ],
            [
                'type' => 'alert',
                'channel' => 'sms',
                'target_users' => 'all',
                'title' => 'تنبيه: تحديث السياسات',
                'message' => 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...',
            ],
            [
                'type' => 'info',
                'channel' => 'push',
                'target_users' => 'tenants',
                'title' => 'عرض ترويجي للمستأجرين',
                'message' => 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...',
            ],
            [
                'type' => 'announcement',
                'channel' => 'email',
                'target_users' => 'landlords',
                'title' => 'دليل التطبيق داخل التطبيق',
                'message' => 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...',
            ],
            [
                'type' => 'alert',
                'channel' => 'push',
                'target_users' => 'all',
                'title' => 'تنبيه أمني هام',
                'message' => 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...',
            ],
            [
                'type' => 'info',
                'channel' => 'sms',
                'target_users' => 'specific',
                'title' => 'تأكيد الدفع',
                'message' => 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...',
            ],
            [
                'type' => 'announcement',
                'channel' => 'email',
                'target_users' => 'all',
                'title' => 'ميزات جديدة في التطبيق',
                'message' => 'لوريم ايبسوم هو نموذج افتراضي يوضع في التصميم لتوضح بيانات الشكل التقليعي المتواجد عن طريق الإدخاع المتاح لمثل هذا الشكل القادمة على المستقبل المواتية...',
            ],
        ];

        foreach ($notificationsData as $index => $data) {
            // Choose how many users to send to based on target_users
            if ($data['target_users'] === 'specific') {
                // Send to 1 random user
                $targetUsers = $users->random(1);
            } elseif ($data['target_users'] === 'tenants') {
                // Send to 3-5 random users (simulating tenants)
                $targetUsers = $users->random(min(rand(3, 5), $users->count()));
            } elseif ($data['target_users'] === 'landlords') {
                // Send to 2-4 random users (simulating landlords)
                $targetUsers = $users->random(min(rand(2, 4), $users->count()));
            } else {
                // Send to all (5-8 users)
                $targetUsers = $users->random(min(rand(5, 8), $users->count()));
            }

            foreach ($targetUsers as $user) {
                Notification::create([
                    'user_id' => $user->id,
                    'type' => $data['type'],
                    'channel' => $data['channel'],
                    'target_users' => $data['target_users'],
                    'title' => $data['title'],
                    'message' => $data['message'],
                    'sent_at' => now()->subDays(rand(0, 30)),
                ]);
            }
        }

        $this->command->info('تم إنشاء '.count($notificationsData).' مجموعة إشعارات وهمية بنجاح!');
    }
}
