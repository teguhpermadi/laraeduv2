<?php

namespace App\Observers;

use App\Models\TeacherQuran;

class TeacherQuranObserver
{
    /**
     * Handle the TeacherQuran "created" event.
     */
    public function created(TeacherQuran $teacherQuran): void
    {
        // tambahkan role teacher_quran
        $teacherQuran->teacher->userable->user->assignRole('teacher_quran');
    }

    /**
     * Handle the TeacherQuran "updated" event.
     */
    public function updated(TeacherQuran $teacherQuran): void
    {
        // periksa apakah teacher id berubah, jika ada teacher baru maka berikan role teacher_quran pada user dari teacher yang baru
        if ($teacherQuran->isDirty('teacher_id')) {
            $teacherQuran->teacher->userable->user->removeRole('teacher_quran');
            $teacherQuran->teacher->userable->user->assignRole('teacher_quran');
        }
    }

    /**
     * Handle the TeacherQuran "deleted" event.
     */
    public function deleted(TeacherQuran $teacherQuran): void
    {
        // cek apakah teacher_id memiliki teacher_grade lain, jika tidak maka hapus role teacher_grade dari user
        if ($teacherQuran->teacher->teacherQuran->count() == 0) {
            $teacherQuran->teacher->userable->user->removeRole('teacher_quran');
        }
    }

    /**
     * Handle the TeacherQuran "restored" event.
     */
    public function restored(TeacherQuran $teacherQuran): void
    {
        //
    }

    /**
     * Handle the TeacherQuran "force deleted" event.
     */
    public function forceDeleted(TeacherQuran $teacherQuran): void
    {
        //
    }
}
