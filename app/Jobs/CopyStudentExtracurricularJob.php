<?php

namespace App\Jobs;

use App\Models\StudentExtracurricular;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            $score = '';

            switch ($studentExtracurricular->score) {
                case 'A':
                    $score = 4;
                    break;
                case 'B':
                    $score = 3;
                    break;
                case 'C':
                    $score = 2;
                    break;
                case 'D':
                    $score = 1;
                    break;
                default:
                    $score = 0;
                    break;
            }

            $data = [
                'id' => $studentExtracurricular->id,
                'student_id' => $studentExtracurricular->student_id,
                'extracurricular_id' => $studentExtracurricular->extracurricular_id,
                'academic_year_id' => $studentExtracurricular->academic_year_id,
                'score' => $score,
            ];

            $array = json_decode(json_encode($data), true);

            StudentExtracurricular::create($array);
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('CopyStudentExtracurricularJob failed: ' . $exception->getMessage());
    }
}
