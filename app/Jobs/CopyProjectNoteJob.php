<?php

namespace App\Jobs;

use App\Models\ProjectNote;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CopyProjectNoteJob implements ShouldQueue
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
        $projectNotes = DB::connection('laraedu')->table('project_notes')->get();

        foreach ($projectNotes as $projectNote) {
            $array = json_decode(json_encode($projectNote), true);

            ProjectNote::create($array);
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('CopyProjectNoteJob failed: ' . $exception->getMessage());
    }
}
