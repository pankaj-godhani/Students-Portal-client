<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName,
            'middle_name' => $this->faker->optional()->firstName,
            'last_name' => $this->faker->lastName,
            'date_of_birth' => $this->faker->date,
            'age' => $this->faker->numberBetween(5, 18),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'monday' => $this->faker->boolean,
            'tuesday' => $this->faker->boolean,
            'wednesday' => $this->faker->boolean,
            'thursday' => $this->faker->boolean,
            'friday' => $this->faker->boolean,
            'saturday' => $this->faker->boolean,
            'sunday' => $this->faker->boolean,
        ];
    }
}
