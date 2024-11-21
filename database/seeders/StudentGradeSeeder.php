<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\Student;
use App\Models\StudentGrade;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentGradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = StudentGrade::factory(100)->make()->toArray();

        // StudentGrade::upsert($data, ['academic_year_id', 'grade_id', 'student_id']);

        foreach ($data as $d) {
            try {
                StudentGrade::create([
                    'academic_year_id' => $d['academic_year_id'],
                    'grade_id' => $d['grade_id'],
                    'student_id' => $d['student_id'],
                ]);
            } catch (\Throwable $th) {
                //
            }
        }
    }
}
