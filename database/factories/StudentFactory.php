<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nisn' => fake()->randomNumber(),
            'nis' => fake()->randomNumber(),
            'name' => fake()->name(),
            'gender' => fake()->randomElement(['laki-laki', 'perempuan']),
            'nick_name' => fake()->firstName(),
            'birthday' => fake()->date(),
            'active' => fake()->randomElement([1,0]),
        ];
    }
}
