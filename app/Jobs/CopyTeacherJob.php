<?php

namespace App\Jobs;

use App\Models\Teacher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CopyTeacherJob implements ShouldQueue
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
        $teachers = DB::connection('laraedu')->table('teachers')->get();

        foreach ($teachers as $teacher) {
            Teacher::create($teacher);
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('CopyTeacherJob failed: ' . $exception->getMessage());
    }
}
