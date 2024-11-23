<?php

namespace App\Observers;

use App\Models\ProjectTarget;
use App\Models\StudentGrade;
use App\Models\StudentProject;

class ProjectTargetObserver
{
    /**
     * Handle the ProjectTarget "created" event.
     */
    public function created(ProjectTarget $projectTarget): void
    {
        // $project = ProjectTarget::with('project.grade.studentGrade')->get();
        $project = $projectTarget->project;
        $students = $project->grade->studentGrade;

        $data = [];
        foreach ($students as $student) {
            $data[] = [
                'academic_year_id' => $project->academic_year_id,
                'student_id' => $student->student_id,
                'project_target_id' => $projectTarget->id,
            ];
        }

        StudentProject::insert($data);
    }

    /**
     * Handle the ProjectTarget "updated" event.
     */
    public function updated(ProjectTarget $projectTarget): void
    {
        // 
    }

    /**
     * Handle the ProjectTarget "deleted" event.
     */
    public function deleted(ProjectTarget $projectTarget): void
    {
        //
    }

    /**
     * Handle the ProjectTarget "restored" event.
     */
    public function restored(ProjectTarget $projectTarget): void
    {
        //
    }

    /**
     * Handle the ProjectTarget "force deleted" event.
     */
    public function forceDeleted(ProjectTarget $projectTarget): void
    {
        //
    }
}
