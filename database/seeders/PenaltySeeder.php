<?php

namespace Database\Seeders;

use App\Models\Penalty;
use Illuminate\Database\Seeder;

class PenaltySeeder extends Seeder
{
    public function run(): void
    {
        if (Penalty::count() < 30) {
            Penalty::factory()->count(60)->create();
        }

        $samples = [
            ['amount' => 150, 'type' => 'cancellation', 'status' => 'pending'],
            ['amount' => 150, 'type' => 'damage', 'status' => 'paid'],
            ['amount' => 150, 'type' => 'violation', 'status' => 'pending'],
            ['amount' => 150, 'type' => 'compensation', 'status' => 'paid'],
            ['amount' => 150, 'type' => 'late_payment', 'status' => 'cancelled'],
        ];

        foreach ($samples as $s) {
            Penalty::factory()->create($s);
        }
    }
}
