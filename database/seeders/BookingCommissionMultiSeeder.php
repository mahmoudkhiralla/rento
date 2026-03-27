<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Property;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BookingCommissionMultiSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = User::updateOrCreate(
            ['email' => 'commission.multi.tenant@local.test'],
            [
                'name' => 'مستأجر اختبارات متعددة',
                'password' => Hash::make('password'),
                'user_type' => 'tenant',
                'status' => 'active',
            ]
        );

        $landlord = User::updateOrCreate(
            ['email' => 'commission.multi.landlord@local.test'],
            [
                'name' => 'مؤجر اختبارات متعددة',
                'password' => Hash::make('password'),
                'user_type' => 'landlord',
                'city' => 'طرابلس',
                'status' => 'active',
                'id_verified' => true,
                'face_verified' => true,
            ]
        );

        $wallet = Wallet::firstOrCreate(['user_id' => $landlord->id], ['balance' => 0, 'points_balance' => 0]);

        // Backup original settings
        $origMethod = Setting::get('commission_calculation_method', 'percentage');
        $origPercent = (float) Setting::get('commission_percentage', 25);
        $origFixed = (float) Setting::get('commission_fixed_value', 0);

        // Group A: percentage method bookings
        Setting::set('commission_calculation_method', 'percentage');
        Setting::set('commission_percentage', 30); // use 30% for test variety
        Setting::set('commission_fixed_value', 0);

        $propA1 = Property::updateOrCreate(
            ['user_id' => $landlord->id, 'title' => 'غرفة نسبة 30% - A1'],
            ['city' => $landlord->city ?? 'طرابلس', 'price' => 80.00, 'description' => 'نسبة 30%', 'approved' => true]
        );
        $propA2 = Property::updateOrCreate(
            ['user_id' => $landlord->id, 'title' => 'غرفة نسبة 30% - A2'],
            ['city' => $landlord->city ?? 'طرابلس', 'price' => 120.00, 'description' => 'نسبة 30%', 'approved' => true]
        );

        $this->createBookingWithCommission($tenant, $propA1, $wallet);
        $this->createBookingWithCommission($tenant, $propA2, $wallet);

        // Group B: fixed method bookings
        Setting::set('commission_calculation_method', 'fixed');
        Setting::set('commission_percentage', 0);
        Setting::set('commission_fixed_value', 15); // use 15 د.ل ثابتة

        $propB1 = Property::updateOrCreate(
            ['user_id' => $landlord->id, 'title' => 'غرفة قيمة ثابتة 15 - B1'],
            ['city' => $landlord->city ?? 'طرابلس', 'price' => 50.00, 'description' => 'قيمة ثابتة', 'approved' => true]
        );
        $propB2 = Property::updateOrCreate(
            ['user_id' => $landlord->id, 'title' => 'غرفة قيمة ثابتة 15 - B2'],
            ['city' => $landlord->city ?? 'طرابلس', 'price' => 200.00, 'description' => 'قيمة ثابتة', 'approved' => true]
        );

        $this->createBookingWithCommission($tenant, $propB1, $wallet);
        $this->createBookingWithCommission($tenant, $propB2, $wallet);

        // Restore original settings
        Setting::set('commission_calculation_method', $origMethod);
        Setting::set('commission_percentage', $origPercent);
        Setting::set('commission_fixed_value', $origFixed);
    }

    private function createBookingWithCommission(User $tenant, Property $property, Wallet $wallet): void
    {
        $start = Carbon::now()->subDays(rand(3, 10))->toDateString();
        $end = Carbon::now()->subDays(rand(1, 2))->toDateString();
        $booking = Booking::updateOrCreate(
            [
                'user_id' => $tenant->id,
                'property_id' => $property->id,
                'start_date' => $start,
                'end_date' => $end,
            ],
            [
                'guests' => rand(1, 3),
                'status' => 'confirmed',
            ]
        );

        $days = Carbon::parse($booking->start_date)->diffInDays(Carbon::parse($booking->end_date));
        $days = max(1, $days);
        $dailyPrice = (float) ($property->price ?? 0);
        $total = round($dailyPrice * $days, 2);

        $method = Setting::get('commission_calculation_method', 'percentage');
        $percent = (float) Setting::get('commission_percentage', 25);
        $fixed = (float) Setting::get('commission_fixed_value', 0);
        $commission = $method === 'percentage' ? round($total * ($percent / 100), 2) : round(min($fixed, $total), 2);
        $commission = max(0, min($commission, $total));
        $net = round($total - $commission, 2);

        $exists = Transaction::where('booking_id', $booking->id)->where('type', 'payment')->exists();
        if (! $exists) {
            $txn = new Transaction;
            $txn->user_id = $wallet->user_id;
            $txn->wallet_id = $wallet->id;
            $txn->amount = $net;
            $txn->type = 'payment';
            $txn->status = 'completed';
            $txn->booking_id = $booking->id;
            $txn->meta = json_encode([
                'reason' => 'تحصيل حجز مؤكد (MultiSeeder)',
                'daily_price' => $dailyPrice,
                'days' => $days,
                'total' => $total,
                'commission_method' => $method,
                'commission_rate' => $method === 'percentage' ? ($percent / 100) : null,
                'commission_value' => $method === 'fixed' ? $fixed : null,
                'commission' => $commission,
                'net_to_wallet' => $net,
                'property_id' => $property->id,
                'tenant_id' => $booking->user_id,
            ]);
            $txn->save();

            $wallet->balance = ($wallet->balance ?? 0) + $net;
            $wallet->save();
        }
    }
}
