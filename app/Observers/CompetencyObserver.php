<?php

namespace App\Observers;

use App\Models\Competency;
use App\Models\StudentCompetency;
use App\Models\TeacherSubject;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CompetencyObserver
{
    /**
     * Handle the Competency "created" event.
     */
    public function created(Competency $competency): void
    {
        $teacher_subject_id = $competency->teacher_subject_id;
        $teacherSubject = TeacherSubject::with('studentGrade')->find($teacher_subject_id);

        foreach ($teacherSubject->studentGrade as $student) {
            $data = [
                'id' => Str::ulid()->toBase32(),
                'teacher_subject_id' => $teacher_subject_id,
                'student_id' => $student->student_id,
                'competency_id' => $competency->id,
                'created_at' => now(),
            ];

            // log info
            Log::info($data);

            // StudentCompetency::create($data);
        }
        
        // dd($students);

        // dapatkan semua competency berdasarkan teacher subject id
        $passing_grades = Competency::where('teacher_subject_id', $teacher_subject_id)->pluck('passing_grade');
        
        // buatkan rata-rata dari passing grade
        $average_passing_grade = $passing_grades->avg();
        
        // update teacher subject dengan average passing grade
        $teacherSubject->update(['passing_grade' => $average_passing_grade]);
    }

    /**
     * Handle the Competency "updated" event.
     */
    public function updated(Competency $competency): void
    {
        $teacher_subject_id = $competency->teacher_subject_id;
        $teacherSubject = TeacherSubject::with('studentGrade')->find($teacher_subject_id);

        // dapatkan semua passing grade pada competency
        $passing_grades = Competency::where('teacher_subject_id', $teacher_subject_id)->pluck('passing_grade');
        
        // buatkan rata-rata dari passing grade
        $average_passing_grade = $passing_grades->avg();
        
        // update teacher subject dengan average passing grade
        $teacherSubject->update(['passing_grade' => $average_passing_grade]);
    }

    /**
     * Handle the Competency "deleted" event.
     */
    public function deleted(Competency $competency): void
    {
        $teacher_subject_id = $competency->teacher_subject_id;
        $teacherSubject = TeacherSubject::with('studentGrade')->find($teacher_subject_id);

        // dapatkan semua passing grade pada competency
        $passing_grades = Competency::where('teacher_subject_id', $teacher_subject_id)->pluck('passing_grade');
        
        // buatkan rata-rata dari passing grade
        $average_passing_grade = $passing_grades->avg();
        
        // update teacher subject dengan average passing grade
        $teacherSubject->update(['passing_grade' => $average_passing_grade]);
    }

    /**
     * Handle the Competency "restored" event.
     */
    public function restored(Competency $competency): void
    {
        $teacher_subject_id = $competency->teacher_subject_id;
        $teacherSubject = TeacherSubject::with('studentGrade')->find($teacher_subject_id);

        // dapatkan semua passing grade pada competency
        $passing_grades = Competency::where('teacher_subject_id', $teacher_subject_id)->pluck('passing_grade');
        
        // buatkan rata-rata dari passing grade
        $average_passing_grade = $passing_grades->avg();
        
        // update teacher subject dengan average passing grade
        $teacherSubject->update(['passing_grade' => $average_passing_grade]);
    }

    /**
     * Handle the Competency "force deleted" event.
     */
    public function forceDeleted(Competency $competency): void
    {
        $teacher_subject_id = $competency->teacher_subject_id;
        $teacherSubject = TeacherSubject::with('studentGrade')->find($teacher_subject_id);

        // dapatkan semua passing grade pada competency
        $passing_grades = Competency::where('teacher_subject_id', $teacher_subject_id)->pluck('passing_grade');
        
        // buatkan rata-rata dari passing grade
        $average_passing_grade = $passing_grades->avg();
        
        // update teacher subject dengan average passing grade
        $teacherSubject->update(['passing_grade' => $average_passing_grade]);
    }
}
