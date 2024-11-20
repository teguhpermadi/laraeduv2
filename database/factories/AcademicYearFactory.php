<?php

namespace Database\Factories;

use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AcademicYear>
 */
class AcademicYearFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'year' => fake()->year(),
            'semester' => fake()->randomElement(['ganjil', 'genap']),
            'teacher_id' => Teacher::get()->random()->id,
            'date_report_half' => fake()->date(),
            'date_report' => fake()->date(),
        ];
    }
}
