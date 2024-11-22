<?php

namespace Database\Factories;

use App\Enums\PhaseEnum;
use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\ProjectTheme;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
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
            'grade_id' => Grade::get()->random()->id,
            'teacher_id' => Teacher::get()->random()->id,
            'name' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'phase' => PhaseEnum::getRandomValue(),
            'project_theme_id' => ProjectTheme::get()->random()->id,
        ];
    }
}
