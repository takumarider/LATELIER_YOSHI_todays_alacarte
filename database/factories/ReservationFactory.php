<?php

namespace Database\Factories;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        return [
            'user_id'          => null,
            'product_id'       => null,
            'customer_name'    => $this->faker->name(),
            'email'            => $this->faker->safeEmail(),
            'phone'            => null,
            'reservation_date' => now()->toDateString(),
            'guest_count'      => 1,
            'status'           => 'pending',
            'note'             => null,
        ];
    }
}
