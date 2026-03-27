<?php

namespace Database\Seeders;

use App\Models\ActivePlace;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivePlacesSeeder extends Seeder
{
    public function run(): void
    {
        // حاول إيجاد مستخدمين موجودين من الديمو أو أنشئ افتراضيين
        $landlord = User::where('user_type', 'landlord')->first();
        if (! $landlord) {
            $landlord = User::factory()->create([
                'name' => 'مؤجر تجريبي',
                'email' => 'landlord.demo@local.test',
                'password' => bcrypt('password'),
                'user_type' => 'landlord',
            ]);
        }

        $tenant = User::where('user_type', 'tenant')->first();
        if (! $tenant) {
            $tenant = User::factory()->create([
                'name' => 'مستأجر تجريبي',
                'email' => 'tenant.demo@local.test',
                'password' => bcrypt('password'),
                'user_type' => 'tenant',
            ]);
        }

        // مؤجر: اسم العقار، المدينة - المنطقة، التاريخ، إيجار يومي، السعر، د.ل / اليوم
        $landlordPlaces = [
            [
                'name' => 'فيلا حديثة بإطلالة جميلة',
                'city' => 'طرابلس',
                'area' => 'حي الأندلس',
                'available_from' => now()->addDays(7)->toDateString(),
                'booking_type' => 'إيجار يومي',
                'price' => 20.00,
                'price_unit' => 'د.ل / اليوم',
                'image' => 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?auto=format&fit=crop&w=600&h=360&q=60',
            ],
            [
                'name' => 'شقة فاخرة بالقرب من البحر',
                'city' => 'بنغازي',
                'area' => 'الكويفية',
                'available_from' => now()->addDays(3)->toDateString(),
                'booking_type' => 'إيجار يومي',
                'price' => 35.00,
                'price_unit' => 'د.ل / اليوم',
                'image' => 'https://images.unsplash.com/photo-1505692794403-43d47a7f368a?auto=format&fit=crop&w=600&h=360&q=60',
            ],
        ];

        foreach ($landlordPlaces as $p) {
            ActivePlace::updateOrCreate([
                'user_id' => $landlord->id,
                'name' => $p['name'],
            ], array_merge($p, [
                'user_id' => $landlord->id,
                'user_type' => 'landlord',
                'rating' => null,
            ]));
        }

        // مستأجر: شقة فاخرة بالقرب من وسط المدينة، المدينة، التقييم، السعر، د.ل / ليلة
        $tenantPlaces = [
            [
                'name' => 'شقة فاخرة بالقرب من وسط المدينة',
                'city' => 'طرابلس',
                'area' => null,
                'available_from' => null,
                'booking_type' => 'إيجار ليلي',
                'price' => 20.00,
                'price_unit' => 'د.ل / ليلة',
                'rating' => 4.5,
                'image' => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c6bc?auto=format&fit=crop&w=600&h=360&q=60',
            ],
            [
                'name' => 'استوديو مريح وقريب من الخدمات',
                'city' => 'مصراتة',
                'area' => null,
                'available_from' => null,
                'booking_type' => 'إيجار ليلي',
                'price' => 18.00,
                'price_unit' => 'د.ل / ليلة',
                'rating' => 4.2,
                'image' => 'https://images.unsplash.com/photo-1495197359483-d092478c170a?auto=format&fit=crop&w=600&h=360&q=60',
            ],
        ];

        foreach ($tenantPlaces as $p) {
            ActivePlace::updateOrCreate([
                'user_id' => $tenant->id,
                'name' => $p['name'],
            ], array_merge($p, [
                'user_id' => $tenant->id,
                'user_type' => 'tenant',
            ]));
        }
    }
}
