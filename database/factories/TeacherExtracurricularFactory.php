<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\Extracurricular;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TeacherExtracurricular>
 */
class TeacherExtracurricularFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'academic_year_id' => AcademicYear::first()->id,
            'teacher_id' => Teacher::get()->random()->id,
            'extracurricular_id' => Extracurricular::get()->random()->id,
        ];
    }
}
