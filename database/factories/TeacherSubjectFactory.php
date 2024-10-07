<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TeacherSubject>
 */
class TeacherSubjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'academic_year_id' => AcademicYear::all()->random()->id,
            'teacher_id' => Teacher::all()->random()->id,
            'grade_id' => Grade::all()->random()->id,
            'subject_id' => Subject::all()->random()->id,
            'time_allocation' => fake()->randomNumber(1),
        ];
    }
}
