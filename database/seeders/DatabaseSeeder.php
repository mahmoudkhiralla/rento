<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            DefaultRolesSeeder::class,
            DemoDataSeeder::class,
            LandlordDemoSeeder::class,
            RichUsersSeeder::class,
            PropertyTypesAndAmenitiesSeeder::class,
            PaymentCardSeeder::class,
            BookingCommissionSeeder::class,
            BookingCommissionMultiSeeder::class,
            PenaltySeeder::class,
        ]);
    }
}
