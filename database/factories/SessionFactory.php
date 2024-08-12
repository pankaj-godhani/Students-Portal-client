<?php

namespace Database\Factories;

use App\Models\Session;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SessionFactory extends Factory
{
    protected $model = Session::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),  // Generates a new user or associates with an existing user
            'start_time' => $this->faker->dateTimeBetween('-1 week', '+1 week'),  // Random start time
            'end_time' => $this->faker->dateTimeBetween('+1 hour', '+3 hours'),  // Random end time
            'target' => $this->faker->sentence,  // Example target description
            'is_repeated' => $this->faker->boolean,  // Random boolean for repetition
        ];
    }
}
