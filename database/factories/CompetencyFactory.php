<?php

namespace Database\Factories;

use App\Models\TeacherSubject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Competency>
 */
class CompetencyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'teacher_subject_id' => TeacherSubject::all()->random()->id,
            'code' => fake()->word(),
            'description' => fake()->sentence(),
            'passing_grade' => fake()->randomDigit(),
            'half_semester' => fake()->randomElement([0,1]),
        ];
    }
}
