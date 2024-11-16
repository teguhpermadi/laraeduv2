<?php

namespace Database\Factories;

use App\Enums\PhaseEnum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Grade>
 */
class GradeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['kelas A', 'kelas B', 'kelas C', 'kelas D', 'kelas E', 'kelas F']),
            'grade' => fake()->randomElement([1,2,3,4,5,6]),
            'phase' => fake()->randomElement(PhaseEnum::class),
        ];
    }
}
