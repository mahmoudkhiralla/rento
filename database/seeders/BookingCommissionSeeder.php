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

class BookingCommissionSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = User::updateOrCreate(
            ['email' => 'commission.tenant@local.test'],
            [
                'name' => 'مستأجر اختبار عمولة',
                'password' => Hash::make('password'),
                'user_type' => 'tenant',
                'status' => 'active',
            ]
        );

        $landlord = User::updateOrCreate(
            ['email' => 'commission.landlord@local.test'],
            [
                'name' => 'مؤجر اختبار عمولة',
                'password' => Hash::make('password'),
                'user_type' => 'landlord',
                'city' => 'طرابلس',
                'status' => 'active',
                'id_verified' => true,
                'face_verified' => true,
            ]
        );

        $property = Property::updateOrCreate(
            ['user_id' => $landlord->id, 'title' => 'غرفة اختبار عمولة'],
            [
                'city' => $landlord->city ?? 'طرابلس',
                'price' => 100.00,
                'description' => 'غرفة لاختبار إظهار إجمالي العمولات.',
                'approved' => true,
                'image' => 'https://images.unsplash.com/photo-1495197359483-d092478c170a?auto=format&fit=crop&w=800&h=500&q=60',
            ]
        );

        $wallet = Wallet::firstOrCreate(['user_id' => $landlord->id], ['balance' => 0, 'points_balance' => 0]);

        $start = Carbon::now()->subDays(5)->toDateString();
        $end = Carbon::now()->subDays(2)->toDateString();
        $booking = Booking::updateOrCreate(
            [
                'user_id' => $tenant->id,
                'property_id' => $property->id,
                'start_date' => $start,
                'end_date' => $end,
            ],
            [
                'guests' => 2,
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
        $commission = $method === 'percentage' ? round($total * ($percent / 100), 2) : round($fixed, 2);
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
                'reason' => 'تحصيل حجز مؤكد (Seeder)',
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
