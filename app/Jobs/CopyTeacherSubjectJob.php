<?php

namespace App\Jobs;

use App\Models\TeacherSubject;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CopyTeacherSubjectJob implements ShouldQueue
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
        $teacherSubjects = DB::connection('laraedu')->table('teacher_subjects')->get();

        foreach ($teacherSubjects as $teacherSubject) {
            $data = [
                'academic_year_id' => $teacherSubject->academic_year_id,
                'teacher_id' => $teacherSubject->teacher_id,
                'subject_id' => $teacherSubject->subject_id,
                'grade_id' => $teacherSubject->grade_id,
            ];

            TeacherSubject::create($data);
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('CopyTeacherSubjectJob failed: ' . $exception->getMessage());
    }
}
