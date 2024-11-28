<?php

namespace App\Observers;

use App\Models\TeacherGrade;

class TeacherGradeObserver
{
    // jika created, tambahkan role teacher_grade ke user
    public function created(TeacherGrade $teacherGrade): void
    {
        $teacherGrade->teacher->userable->user->assignRole('teacher_grade');
    }

    /**
     * Handle the TeacherGrade "updated" event.
     */
    public function updated(TeacherGrade $teacherGrade): void
    {
        // periksa apakah teacher id berubah, jika ada teacher baru maka berikan role teacher_grade pada user dari teacher yang baru
        if ($teacherGrade->isDirty('teacher_id')) {
            $teacherGrade->teacher->userable->user->removeRole('teacher_grade');
            $teacherGrade->teacher->userable->user->assignRole('teacher_grade');
        }
    }

    // jika deleted, hapus role teacher_grade dari user
    public function deleted(TeacherGrade $teacherGrade): void
    {
        // cek apakah teacher_id memiliki teacher_grade lain, jika tidak maka hapus role teacher_grade dari user
        if ($teacherGrade->teacher->teacherGrade->count() == 0) {
            $teacherGrade->teacher->userable->user->removeRole('teacher_grade');
        }
    }
}
