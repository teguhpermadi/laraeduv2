<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\QuranGrade;
use App\Models\Student;
use App\Models\StudentQuranGrade;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Database\QueryException;

class StudentQuranGradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = StudentQuranGrade::factory(10)->make()->toArray();

        StudentQuranGrade::upsert($data, ['academic_year_id', 'student_id', 'quran_grade_id']);
    }
}
