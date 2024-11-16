<?php

namespace App\Observers;

use App\Models\CompetencyQuran;
use App\Models\Scopes\AcademicYearScope;
use App\Models\TeacherQuranGrade;
use App\Models\StudentCompetencyQuran;
class CompetencyQuranObserver
{
    /**
     * Handle the CompetencyQuran "created" event.
     */
    public function created(CompetencyQuran $competencyQuran): void
    {
        $teacherQuranGrade = TeacherQuranGrade::withoutGlobalScope(AcademicYearScope::class)
            ->with('studentQuranGrade')
            ->find($competencyQuran->teacher_quran_grade_id);

        $data = [];

        $countStudent = $teacherQuranGrade->studentQuranGrade->count();

        // if studentqurangrade exists, then create    
        if ($countStudent > 0) {
            foreach ($teacherQuranGrade->studentQuranGrade as $student) {
                $data[] = [
                    'academic_year_id' => session('academic_year_id'),
                    'quran_grade_id' => $teacherQuranGrade->quranGrade->id,
                    'student_quran_grade_id' => $student->id,
                    'competency_quran_id' => $competencyQuran->id,
                    'created_at' => now(),
                ];
            }
            StudentCompetencyQuran::insert($data);
        }
    }

    /**
     * Handle the CompetencyQuran "updated" event.
     */
    public function updated(CompetencyQuran $competencyQuran): void
    {
        //
    }

    /**
     * Handle the CompetencyQuran "deleted" event.
     */
    public function deleted(CompetencyQuran $competencyQuran): void
    {
        //
    }

    /**
     * Handle the CompetencyQuran "restored" event.
     */
    public function restored(CompetencyQuran $competencyQuran): void
    {
        //
    }

    /**
     * Handle the CompetencyQuran "force deleted" event.
     */
    public function forceDeleted(CompetencyQuran $competencyQuran): void
    {
        //
    }
}
