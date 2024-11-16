<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Extracurricular;
use App\Models\Teacher;
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
            'academic_year_id' => AcademicYear::first()->id,
            'teacher_id' => Teacher::get()->random()->id,
            'extracurricular_id' => Extracurricular::get()->random()->id,
        ]);

        TeacherExtracurricular::create([
            'academic_year_id' => AcademicYear::first()->id,
            'teacher_id' => Teacher::get()->random()->id,
            'extracurricular_id' => Extracurricular::get()->random()->id,
        ]);
    }
}
