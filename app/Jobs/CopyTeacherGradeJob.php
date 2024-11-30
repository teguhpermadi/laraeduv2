<?php

namespace App\Jobs;

use App\Models\TeacherGrade;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CopyTeacherGradeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $teacherGrades = DB::connection('laraedu')->table('teacher_grades')->get();

        foreach ($teacherGrades as $teacherGrade) {
            $data = [
                'academic_year_id' => $teacherGrade->academic_year_id,
                'teacher_id' => $teacherGrade->teacher_id,
                'grade_id' => $teacherGrade->grade_id,
                'curriculum' => $teacherGrade->curriculum,
            ];

            TeacherGrade::create($data);
        }
    }
}
