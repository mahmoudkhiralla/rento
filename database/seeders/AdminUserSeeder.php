<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء حساب الأدمن الأول في جدول admins
        Admin::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'meropubg250@gmail.com')],
            [
                'name' => env('ADMIN_NAME', 'مشرف النظام'),
                'password' => bcrypt(env('ADMIN_PASSWORD', 'mero1234')),
                'phone' => env('ADMIN_PHONE', null),
                'image' => env('ADMIN_IMAGE', null),
            ]
        );
    }
}
