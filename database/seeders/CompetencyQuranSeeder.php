<?php

namespace Database\Seeders;

use App\Models\CompetencyQuran;
use App\Models\TeacherQuranGrade;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Database\QueryException;

class CompetencyQuranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CompetencyQuran::factory(5)->create();
    }
}
