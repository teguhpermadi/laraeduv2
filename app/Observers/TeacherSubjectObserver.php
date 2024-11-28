<?php

namespace App\Observers;

use App\Enums\CategoryLegerEnum;
use App\Models\Competency;
use App\Models\TeacherSubject;
use App\Models\TeacherSubjectNote;

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
            'code' => CategoryLegerEnum::HALF_SEMESTER->value,
            'description' => CategoryLegerEnum::HALF_SEMESTER->getLabel(),
            'passing_grade' => 70,
            'half_semester' => true,
        ]);

        // AKHIR SEMESTER
        Competency::create([
            'teacher_subject_id' => $teacherSubject->id,
            'code' => CategoryLegerEnum::FULL_SEMESTER->value,
            'description' => CategoryLegerEnum::FULL_SEMESTER->getLabel(),
            'passing_grade' => 70,
            'half_semester' => false,
        ]);

        // berikan role teacher_subject kepada user
        $teacherSubject->teacher->userable->user->assignRole('teacher_subject');
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
        // cek apakah teacher_id memiliki teacher_subject lain, jika tidak maka hapus role teacher_subject dari user
        if ($teacherSubject->teacher->subject->count() == 0) {
            $teacherSubject->teacher->userable->user->removeRole('teacher_subject');
        }
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
