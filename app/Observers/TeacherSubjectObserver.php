<?php

namespace App\Observers;

use App\Models\Competency;
use App\Models\TeacherSubject;

class TeacherSubjectObserver
{
    /**
     * Handle the TeacherSubject "created" event.
     */
    public function created(TeacherSubject $teacherSubject): void
    {
        // TENGAH SEMESTER
        Competency::create([
            'teacher_subject_id' => $teacherSubject->id,
            'code' => "TENGAH SEMESTER",
            'description' => 'TENGAH SEMESTER',
            'passing_grade' => 70,
        ]);

        // AKHIR SEMESTER
        Competency::create([
            'teacher_subject_id' => $teacherSubject->id,
            'code' => "AKHIR SEMESTER",
            'description' => 'AKHIR SEMESTER',
            'passing_grade' => 70,
        ]);
    }

    /**
     * Handle the TeacherSubject "updated" event.
     */
    public function updated(TeacherSubject $teacherSubject): void
    {
        //
    }

    /**
     * Handle the TeacherSubject "deleted" event.
     */
    public function deleted(TeacherSubject $teacherSubject): void
    {
        //
    }

    /**
     * Handle the TeacherSubject "restored" event.
     */
    public function restored(TeacherSubject $teacherSubject): void
    {
        //
    }

    /**
     * Handle the TeacherSubject "force deleted" event.
     */
    public function forceDeleted(TeacherSubject $teacherSubject): void
    {
        //
    }
}