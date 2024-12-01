<?php

namespace App\Jobs;

use App\Models\DataStudent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CopyDataStudentJob implements ShouldQueue
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
        $dataStudents = DB::connection('laraedu')->table('data_students')->get();

        foreach ($dataStudents as $dataStudent) {
            $array = json_decode(json_encode($dataStudent), true);

            DataStudent::create($array);
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('CopyDataStudentJob failed: ' . $exception->getMessage());
    }
}
