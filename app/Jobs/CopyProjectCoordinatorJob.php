<?php

namespace App\Jobs;

use App\Models\ProjectCoordinator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CopyProjectCoordinatorJob implements ShouldQueue
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
        $projectCoordinators = DB::connection('laraedu')->table('project_coordinators')->get();

        foreach ($projectCoordinators as $projectCoordinator) {
            ProjectCoordinator::create($projectCoordinator);
        }
    }
}
