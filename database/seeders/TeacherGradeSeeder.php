<?php

namespace Database\Seeders;

use App\Models\TeacherGrade;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeacherGradeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = TeacherGrade::factory(10)->make()->toArray();

        TeacherGrade::upsert($data, ['academic_year_id', 'grade_id'], ['teacher_id']);
    }
}
