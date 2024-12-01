<?php

namespace App\Jobs;

use App\Models\StudentProject;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CopyProjectStudentJob implements ShouldQueue
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
        $projectStudents = DB::connection('laraedu')->table('project_students')->get();

        foreach ($projectStudents as $projectStudent) {
            $array = json_decode(json_encode($projectStudent), true);

            StudentProject::create($array);
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('CopyProjectStudentJob failed: ' . $exception->getMessage());
    }
}
