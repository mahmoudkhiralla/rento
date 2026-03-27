<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Penalty;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PenaltyFactory extends Factory
{
    protected $model = Penalty::class;

    public function definition()
    {
        $types = ['late_payment', 'damage', 'cancellation', 'violation', 'compensation'];
        $statuses = ['pending', 'paid', 'cancelled'];
        $userId = User::inRandomOrder()->value('id');
        $bookingId = Booking::inRandomOrder()->value('id');

        return [
            'user_id' => $userId,
            'booking_id' => $bookingId,
            'amount' => $this->faker->randomElement([50, 75, 100, 150, 200, 250, 300]),
            'type' => $this->faker->randomElement($types),
            'reason' => $this->faker->sentence(10),
            'status' => $this->faker->randomElement($statuses),
            'paid_at' => null,
            'notes' => $this->faker->boolean(30) ? $this->faker->sentence(12) : null,
        ];
    }
}
