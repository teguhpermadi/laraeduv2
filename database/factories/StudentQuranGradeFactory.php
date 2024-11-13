<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\QuranGrade;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StudentQuranGrade>
 */
class StudentQuranGradeFactory extends Factory
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
            'student_id' => Student::all()->random()->id,
            'quran_grade_id' => QuranGrade::all()->random()->id,
        ];
    }
}
