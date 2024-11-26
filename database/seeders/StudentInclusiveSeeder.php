<?php

namespace Database\Seeders;

use App\Models\StudentInclusive;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentInclusiveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StudentInclusive::factory()->count(10)->create();
    }
}
