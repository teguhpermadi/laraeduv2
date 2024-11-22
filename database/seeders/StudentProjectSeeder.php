<?php

namespace Database\Seeders;

use App\Enums\LinkertScaleEnum;
use App\Models\Project;
use App\Models\StudentProject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = Project::with('projectTarget', 'grade')->get();

        $data = [];
        foreach ($projects as $project) {
            // setiap project target
            $projectTargets = $project->projectTarget;

            foreach ($projectTargets as $projectTarget) {
                $students = $project->grade->studentGrade;
                foreach ($students as $student) {
                    $data[] = [
                        'academic_year_id'  => $project->academic_year_id,
                        'student_id'        => $student->student_id,
                        'project_target_id' => $projectTarget->id,
                        'score'             => LinkertScaleEnum::getRandomValue(),
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ];
                }
            }
        }

        StudentProject::insert($data);
    }
}
