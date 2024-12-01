<?php

namespace App\Jobs;

use App\Models\Competency;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CopyCompetencyJob implements ShouldQueue
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
        $competencies = DB::connection('laraedu')->table('competencies')->get();

        foreach ($competencies as $competency) {
            
            $array = json_decode(json_encode($competency), true);

            try {
                Competency::create($array);
            } catch (\Throwable $th) {
                Log::error('CopyCompetencyJob failed: ' . $th->getMessage());
            }
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('CopyCompetencyJob failed: ' . $exception->getMessage());
    }
}
