<?php

namespace Database\Factories;

use App\Enums\CategoryInclusive;
use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudentInclusive>
 */
class StudentInclusiveFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'academic_year_id' => AcademicYear::get()->random()->id,
            'student_id' => Student::get()->random()->id,
            'teacher_id' => Teacher::get()->random()->id,
            'category_inclusive' => fake()->randomElement(CategoryInclusive::class),
        ];
    }
}
