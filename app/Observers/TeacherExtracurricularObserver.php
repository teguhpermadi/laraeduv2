<?php

namespace App\Observers;

use App\Models\TeacherExtracurricular;

class TeacherExtracurricularObserver
{
    /**
     * Handle the TeacherExtracurricular "created" event.
     */
    public function created(TeacherExtracurricular $teacherExtracurricular): void
    {
        // buat role teacher_extracurricular ke user
        $teacherExtracurricular->teacher->userable->user->assignRole('teacher_extracurricular');
    }

    /**
     * Handle the TeacherExtracurricular "updated" event.
     */
    public function updated(TeacherExtracurricular $teacherExtracurricular): void
    {
        //
    }

    /**
     * Handle the TeacherExtracurricular "deleted" event.
     */
    public function deleted(TeacherExtracurricular $teacherExtracurricular): void
    {
        // cek apakah teacher_id memiliki teacher_extracurricular lain, jika tidak maka hapus role teacher_extracurricular dari user
        if ($teacherExtracurricular->teacher->teacherExtracurricular->count() == 0) {
            $teacherExtracurricular->teacher->userable->user->removeRole('teacher_extracurricular');
        }
    }

    /**
     * Handle the TeacherExtracurricular "restored" event.
     */
    public function restored(TeacherExtracurricular $teacherExtracurricular): void
    {
        //
    }

    /**
     * Handle the TeacherExtracurricular "force deleted" event.
     */
    public function forceDeleted(TeacherExtracurricular $teacherExtracurricular): void
    {
        //
    }
}
