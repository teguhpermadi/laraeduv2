<?php

namespace Database\Seeders;

use App\Models\Grade;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $grade = Grade::factory(3)->make()->toArray();

        Grade::upsert($grade, uniqueBy:['name', 'grade'], update: ['grade']);
    }
}
