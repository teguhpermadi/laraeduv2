<?php

namespace App\Jobs;

use App\Models\StudentCompetency;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CopyStudentCompetencyJob implements ShouldQueue
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
        $studentCompetencies = DB::connection('laraedu')->table('student_competencies')->get();

        foreach ($studentCompetencies as $studentCompetency) {
            $array = json_decode(json_encode($studentCompetency), true);

            StudentCompetency::create($array);
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('CopyStudentCompetencyJob failed: ' . $exception->getMessage());
    }
}
