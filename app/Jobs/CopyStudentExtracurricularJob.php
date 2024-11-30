<?php

namespace App\Jobs;

use App\Models\StudentExtracurricular;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CopyStudentExtracurricularJob implements ShouldQueue
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
        $studentExtracurriculars = DB::connection('laraedu')->table('student_extracurriculars')->get();

        foreach ($studentExtracurriculars as $studentExtracurricular) {
            $data = [
                'student_id' => $studentExtracurricular->student_id,
                'extracurricular_id' => $studentExtracurricular->extracurricular_id,
                'academic_year_id' => $studentExtracurricular->academic_year_id,
                'score' => $studentExtracurricular->score,
            ];

            StudentExtracurricular::create($data);
        }
    }
}
