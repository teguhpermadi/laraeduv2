<?php

namespace App\Observers;

use App\Models\Competency;
use App\Models\StudentCompetency;
use App\Models\TeacherSubject;

class CompetencyObserver
{
    /**
     * Handle the Competency "created" event.
     */
    public function created(Competency $competency): void
    {
        $teacher_subject_id = $competency->teacher_subject_id;
        $students = TeacherSubject::with('studentGrade')->find($teacher_subject_id);

        $data = [];
        foreach ($students->studentGrade as $student) {
            $data[] = [
                'teacher_subject_id' => $teacher_subject_id,
                'student_id' => $student->student_id,
                'competency_id' => $competency->id,
                'created_at' => now(),
            ];
        }

        StudentCompetency::insert($data);
    }

    /**
     * Handle the Competency "updated" event.
     */
    public function updated(Competency $competency): void
    {
        $teacher_subject_id = $competency->teacher_subject_id;
        $students = TeacherSubject::with('studentGrade')->find($teacher_subject_id);

        $data = [];
        foreach ($students->studentGrade as $student) {
            $data[] = [
                'teacher_subject_id' => $teacher_subject_id,
                'student_id' => $student->student_id,
                'competency_id' => $competency->id,
                'score' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        StudentCompetency::upsert($data, uniqueBy: ['student_id', 'competency_id'], update: ['score']);
    }

    /**
     * Handle the Competency "deleted" event.
     */
    public function deleted(Competency $competency): void
    {
        //
    }

    /**
     * Handle the Competency "restored" event.
     */
    public function restored(Competency $competency): void
    {
        //
    }

    /**
     * Handle the Competency "force deleted" event.
     */
    public function forceDeleted(Competency $competency): void
    {
        //
    }
}
