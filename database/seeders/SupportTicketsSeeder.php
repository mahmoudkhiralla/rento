<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use App\Models\User;
use Illuminate\Database\Seeder;

class SupportTicketsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some users for testing
        $users = User::inRandomOrder()->limit(5)->get();

        // If no users exist, create some
        if ($users->isEmpty()) {
            $users = collect([
                User::create([
                    'name' => 'أحمد محمد',
                    'email' => 'ahmed@test.com',
                    'password' => bcrypt('password'),
                    'user_type' => 'tenant',
                    'phone' => '0123456789',
                ]),
                User::create([
                    'name' => 'فاطمة علي',
                    'email' => 'fatima@test.com',
                    'password' => bcrypt('password'),
                    'user_type' => 'landlord',
                    'phone' => '0123456790',
                ]),
                User::create([
                    'name' => 'محمد حسن',
                    'email' => 'mohamed@test.com',
                    'password' => bcrypt('password'),
                    'user_type' => 'tenant',
                    'phone' => '0123456791',
                ]),
            ]);
        }

        // Get admin for assignment
        $admin = Admin::first();

        // Sample ticket data
        $tickets = [
            [
                'subject' => 'مشكلة في عملية الدفع',
                'description' => 'أواجه صعوبة في إتمام عملية الدفع عبر بطاقة الائتمان. الرجاء المساعدة في حل هذه المشكلة في أقرب وقت ممكن.',
                'status' => 'open',
                'priority' => 'high',
                'category' => 'الدفع والمعاملات المالية',
            ],
            [
                'subject' => 'استفسار عن إلغاء الحجز',
                'description' => 'أود الاستفسار عن سياسة إلغاء الحجز وكيفية استرداد المبلغ المدفوع.',
                'status' => 'in_progress',
                'priority' => 'medium',
                'category' => 'الحجوزات',
            ],
            [
                'subject' => 'تحديث بيانات الملف الشخصي',
                'description' => 'لا أستطيع تحديث بيانات الملف الشخصي الخاص بي. الرجاء المساعدة.',
                'status' => 'resolved',
                'priority' => 'low',
                'category' => 'الحساب والملف الشخصي',
            ],
            [
                'subject' => 'شكوى بخصوص العقار',
                'description' => 'العقار الذي حجزته لا يطابق الوصف والصور المعروضة على المنصة. هناك العديد من المشاكل التي لم تذكر في الإعلان.',
                'status' => 'open',
                'priority' => 'urgent',
                'category' => 'شكاوى العقارات',
            ],
            [
                'subject' => 'مشكلة تقنية في التطبيق',
                'description' => 'التطبيق يتوقف عن العمل بشكل متكرر عند محاولة تصفح العقارات المتاحة.',
                'status' => 'in_progress',
                'priority' => 'high',
                'category' => 'المشاكل التقنية',
            ],
            [
                'subject' => 'طلب فاتورة',
                'description' => 'أحتاج إلى فاتورة رسمية للحجز رقم #12345 لأغراض المحاسبة.',
                'status' => 'closed',
                'priority' => 'low',
                'category' => 'الفواتير والإيصالات',
            ],
            [
                'subject' => 'استفسار عن برنامج الولاء',
                'description' => 'كيف يمكنني الاستفادة من نقاط المكافآت في برنامج الولاء؟',
                'status' => 'resolved',
                'priority' => 'low',
                'category' => 'برامج المكافآت',
            ],
            [
                'subject' => 'مشكلة في التواصل مع المؤجر',
                'description' => 'حاولت التواصل مع المؤجر عدة مرات لكن لم أحصل على أي رد.',
                'status' => 'open',
                'priority' => 'medium',
                'category' => 'التواصل',
            ],
        ];

        foreach ($tickets as $index => $ticketData) {
            $user = $users->random();

            // Determine submitted_by and try to attach explicit links
            $submittedBy = in_array(($user->user_type ?? null), ['landlord', 'both'], true) ? 'landlord' : 'tenant';

            $booking = null;
            if ($submittedBy === 'tenant') {
                $booking = \App\Models\Booking::with(['property.user'])
                    ->where('user_id', $user->id)
                    ->latest()
                    ->first();
            } else {
                $booking = \App\Models\Booking::with(['property.user'])
                    ->whereHas('property', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    })
                    ->latest()
                    ->first();
            }

            $landlordId = $submittedBy === 'landlord' ? $user->id : optional($booking?->property?->user)->id;
            $tenantId = $submittedBy === 'tenant' ? $user->id : optional($booking?->user)->id;
            $propertyId = optional($booking?->property)->id;
            $bookingId = optional($booking)->id;

            $ticket = SupportTicket::create([
                'user_id' => $user->id,
                'submitted_by' => $submittedBy,
                'tenant_id' => $tenantId,
                'landlord_id' => $landlordId,
                'property_id' => $propertyId,
                'booking_id' => $bookingId,
                'subject' => $ticketData['subject'],
                'description' => $ticketData['description'],
                'status' => $ticketData['status'],
                'priority' => $ticketData['priority'],
                'category' => $ticketData['category'],
                'assigned_to' => $admin?->id,
                'last_replied_at' => in_array($ticketData['status'], ['in_progress', 'resolved', 'closed']) ? now()->subHours(rand(1, 48)) : null,
            ]);

            // Add some replies to some tickets
            if (in_array($ticketData['status'], ['in_progress', 'resolved', 'closed'])) {
                // User reply
                SupportTicketReply::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $user->id,
                    'message' => 'شكراً لكم على الاهتمام. أود إضافة بعض التفاصيل الإضافية حول المشكلة...',
                    'is_admin_reply' => false,
                    'created_at' => now()->subHours(rand(24, 47)),
                ]);

                // Admin reply
                if ($admin) {
                    SupportTicketReply::create([
                        'ticket_id' => $ticket->id,
                        'user_id' => $admin->id,
                        'message' => 'شكراً لتواصلك معنا. نحن نعمل على حل المشكلة وسنقوم بإبلاغك بالتحديثات قريباً.',
                        'is_admin_reply' => true,
                        'created_at' => now()->subHours(rand(1, 23)),
                    ]);
                }
            }

            // Add extra replies to resolved/closed tickets
            if (in_array($ticketData['status'], ['resolved', 'closed'])) {
                if ($admin) {
                    SupportTicketReply::create([
                        'ticket_id' => $ticket->id,
                        'user_id' => $admin->id,
                        'message' => 'تم حل المشكلة بنجاح. نتمنى أن تكون الخدمة قد أرضتكم.',
                        'is_admin_reply' => true,
                        'created_at' => now()->subHours(rand(1, 12)),
                    ]);
                }
            }
        }

        $this->command->info('تم إنشاء '.count($tickets).' تذكرة دعم وهمية بنجاح!');
    }
}
