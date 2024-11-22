<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Scopes\AcademicYearScope;
use App\Models\TeacherGrade;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teacherGrade = TeacherGrade::withoutGlobalScope(AcademicYearScope::class)->get();

        foreach ($teacherGrade as $teacher) {
            Project::factory(1)->state([
                'teacher_id' => $teacher->teacher_id,
            ])->create();
        }
    }
}
