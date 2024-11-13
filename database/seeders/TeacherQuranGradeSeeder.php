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
        $data = TeacherQuranGrade::factory(5)->make()->toArray();

        TeacherQuranGrade::upsert($data, ['academic_year_id', 'teacher_id', 'quran_grade_id'], ['created_at', 'updated_at']);
    }
}
