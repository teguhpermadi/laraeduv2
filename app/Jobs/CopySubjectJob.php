<?php

namespace App\Jobs;

use App\Models\Subject;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CopySubjectJob implements ShouldQueue
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
        $subjects = DB::connection('laraedu')->table('subjects')->get();

        foreach ($subjects as $subject) {
            $array = json_decode(json_encode($subject), true);

            Subject::create($array);
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('CopySubjectJob failed: ' . $exception->getMessage());
    }
}
