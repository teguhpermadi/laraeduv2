<?php

namespace App\Jobs;

use App\Models\Userable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CopyUserableJob implements ShouldQueue
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
        $userables = DB::connection('laraedu')->table('userables')->get();

        foreach ($userables as $userable) {
            $array = json_decode(json_encode($userable), true);
            Userable::create($array);
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('CopyUserableJob failed: ' . $exception->getMessage());
    }
}
