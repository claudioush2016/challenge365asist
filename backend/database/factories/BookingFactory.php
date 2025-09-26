<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Booking;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition()
    {
        return [
            'flight_number' => $this->faker->unique()->numberBetween(100, 999), // Provide a unique flight number
            'departure_time' => $this->faker->dateTimeBetween('+1 day', '+1 week'), // Provide a valid departure time
            'status' => 'PENDING', // Provide a default status
        ];
    }
}
