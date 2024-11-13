<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\QuranGrade;
use App\Models\Teacher;
use App\Models\TeacherQuranGrade;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeacherQuranGradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TeacherQuranGrade::factory(5)->create();
    }
}
