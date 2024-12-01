<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use App\Models\StudentGrade;
use Illuminate\Support\Facades\Log;

class CopyStudentGradeJob implements ShouldQueue
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
        $studentGrades = DB::connection('laraedu')->table('student_grades')->get();

        foreach ($studentGrades as $studentGrade) {
            $data = [
                'academic_year_id' => $studentGrade->academic_year_id,
                'student_id' => $studentGrade->student_id,
                'grade_id' => $studentGrade->grade_id,
            ];

            StudentGrade::create($data);
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('CopyStudentGradeJob failed: ' . $exception->getMessage());
    }
}
