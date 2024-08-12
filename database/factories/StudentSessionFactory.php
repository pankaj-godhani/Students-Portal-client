<?php

namespace Database\Factories;

use App\Models\StudentSession;
use App\Models\Student;
use App\Models\Session;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentSessionFactory extends Factory
{
    protected $model = StudentSession::class;

    public function definition()
    {
        return [
            'student_id' => Student::factory(), // Assuming you have a StudentFactory
            'session_id' => Session::factory(), // Assuming you have a SessionFactory
            'rating' => $this->faker->numberBetween(1, 10),
        ];
    }
}
