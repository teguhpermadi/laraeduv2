<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\Extracurricular;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudentExtracurricular>
 */
class StudentExtracurricularFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => Student::get()->random()->id,
            'extracurricular_id' => Extracurricular::get()->random()->id,
            'academic_year_id' => AcademicYear::get()->random()->id,
            'score' => fake()->numberBetween(0, 4),
        ];
    }
}
