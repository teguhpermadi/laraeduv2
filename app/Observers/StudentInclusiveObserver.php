<?php

namespace App\Observers;

use App\Models\StudentInclusive;
use App\Models\TeacherSubject;
use App\Models\TeacherSubjectInclusive;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;

class StudentInclusiveObserver
{
    public function created(StudentInclusive $studentInclusive)
    {
        try {
            // dapatkan semua teacher subject yang terdapat dalam grade ini
            $subjects = TeacherSubject::where('grade_id', $studentInclusive->grade_id)->get();

            foreach ($subjects as $subject) {
                // buat teacher subject inclusive
                TeacherSubjectInclusive::create([
                    'academic_year_id' => session('academic_year_id'),
                    'teacher_id' => $studentInclusive->teacher_id,
                    'subject_id' => $subject->subject_id,
                    'grade_id' => $studentInclusive->grade_id,
                ]);
            }
            
        } catch (\Exception $e) {
            Log::error('Error updating student status: ' . $e->getMessage());
        }
    }
}
