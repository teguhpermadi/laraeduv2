<?php

namespace Database\Seeders;

use App\Models\TeacherExtracurricular;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeacherExtracurricularSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TeacherExtracurricular::create([
            'academic_year_id' => 1,
            'teacher_id' => 1,
            'extracurricular_id' => 1,
        ]);

        TeacherExtracurricular::create([
            'academic_year_id' => 1,
            'teacher_id' => 1,
            'extracurricular_id' => 2,
        ]);
    }
}
