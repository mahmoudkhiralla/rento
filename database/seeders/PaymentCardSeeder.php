<?php

namespace Database\Seeders;

use App\Models\PaymentCard;
use App\Models\User;
use Illuminate\Database\Seeder;

class PaymentCardSeeder extends Seeder
{
    public function run(): void
    {
        $amounts = [25, 50, 100, 200, 500];
        $statuses = ['pending', 'active', 'expired', 'cancelled'];

        for ($i = 0; $i < 50; $i++) {
            $issue = now()->subDays(rand(0, 30));
            $expiry = (clone $issue)->addDays(rand(15, 45));
            $amount = $amounts[array_rand($amounts)];
            $balance = max(0, $amount - rand(0, $amount));

            $userId = null;
            if (rand(0, 1)) {
                $userId = User::inRandomOrder()->value('id');
            }

            PaymentCard::create([
                'user_id' => $userId,
                'card_number' => $this->generateCardNumber(),
                'card_type' => 'standard',
                'amount' => $amount,
                'balance' => $balance,
                'issue_date' => $issue,
                'expiry_date' => $expiry,
                'status' => $statuses[array_rand($statuses)],
                'notes' => null,
            ]);
        }
    }

    private function generateCardNumber(): string
    {
        $digits = '';
        for ($i = 0; $i < 16; $i++) {
            $digits .= random_int(0, 9);
        }

        return substr($digits, 0, 4).' - '.substr($digits, 4, 4).' - '.substr($digits, 8, 4).' - '.substr($digits, 12, 4);
    }
}
