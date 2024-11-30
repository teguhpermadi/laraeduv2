<?php

namespace App\Jobs;

use App\Models\TeacherExtracurricular;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CopyTeacherExtracurricularJob implements ShouldQueue
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
        $teacherExtracurriculars = DB::connection('laraedu')->table('teacher_extracurriculars')->get();
        
        foreach ($teacherExtracurriculars as $teacherExtracurricular) {
            TeacherExtracurricular::create($teacherExtracurricular);
        }
    }
}
