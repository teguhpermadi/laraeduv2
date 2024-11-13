<?php

namespace Database\Factories;

use App\Models\Scopes\AcademicYearScope;
use App\Models\TeacherQuranGrade;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CompetencyQuran>
 */
class CompetencyQuranFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $teacherQuranGrade = TeacherQuranGrade::withoutGlobalScope(AcademicYearScope::class)->get()->random()->id;
        return [
            'teacher_quran_grade_id' => $teacherQuranGrade,
            'code' => fake()->unique()->numerify('C####'),
            'description' => fake()->sentence(),
            'passing_grade' => 70,
        ];
    }
}
