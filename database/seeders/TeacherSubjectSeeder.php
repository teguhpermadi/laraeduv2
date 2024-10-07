<?php

namespace Database\Seeders;

use App\Models\TeacherSubject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeacherSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = TeacherSubject::factory(20)->make()->toArray();

        TeacherSubject::upsert($data, ['academic_year_id', 'grade_id', 'subject_id'], ['teacher_id']);
    }
}
