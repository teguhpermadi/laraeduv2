<?php

namespace App\Jobs;

use App\Models\Extracurricular;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CopyExtracurricularJob implements ShouldQueue
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
        $extracurriculars = DB::connection('laraedu')->table('extracurriculars')->get();

        foreach ($extracurriculars as $extracurricular) {
            $array = json_decode(json_encode($extracurricular), true);

            Extracurricular::create($array);
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('CopyExtracurricularJob failed: ' . $exception->getMessage());
    }
}
