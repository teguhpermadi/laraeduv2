<?php

namespace App\Observers;

use App\Models\StudentQuranGrade;

class StudentQuranGradeObserver
{
    /**
     * Handle the StudentQuranGrade "created" event.
     */
    public function created(StudentQuranGrade $studentQuranGrade): void
    {
        //
    }

    /**
     * Handle the StudentQuranGrade "updated" event.
     */
    public function updated(StudentQuranGrade $studentQuranGrade): void
    {
        //
    }

    /**
     * Handle the StudentQuranGrade "deleted" event.
     */
    public function deleted(StudentQuranGrade $studentQuranGrade): void
    {
        $academicYearId = $studentQuranGrade->academic_year_id;
        $quranGradeId = $studentQuranGrade->quran_grade_id;
        $studentId = $studentQuranGrade->student_id;

        // Hapus semua StudentCompetencyQuran yang terkait dengan student_id ini
        $studentQuranGrade->studentCompetencyQuran()
            ->where('academic_year_id', $academicYearId)
            ->where('quran_grade_id', $quranGradeId)
            ->where('student_id', $studentId)
            ->delete();
    }

    /**
     * Handle the StudentQuranGrade "restored" event.
     */
    public function restored(StudentQuranGrade $studentQuranGrade): void
    {
        //
    }

    /**
     * Handle the StudentQuranGrade "force deleted" event.
     */
    public function forceDeleted(StudentQuranGrade $studentQuranGrade): void
    {
        //
    }
}
