<?php

namespace App\Jobs;

use App\Enums\PhaseEnum;
use App\Models\Project;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CopyProjectJob implements ShouldQueue
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
        $projects = DB::connection('laraedu')->table('projects')->get();

        foreach ($projects as $project) {
            $fase = $project->phase;
            $phase = null;

            switch ($fase) {
                case 'a':
                    $phase = PhaseEnum::phaseA->value;
                    break;
                case 'b':
                    $phase = PhaseEnum::phaseB->value;
                    break;
                case 'c':
                    $phase = PhaseEnum::phaseC->value;
                    break;
            }

            $data = [
                'academic_year_id' => $project->academic_year_id,
                'grade_id' => $project->grade_id,
                'teacher_id' => $project->teacher_id,
                'project_theme_id' => $project->project_theme_id,
                'name' => $project->name,
                'description' => $project->description,
                'phase' => $phase,
            ];

            Project::create($data);
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('CopyProjectJob failed: ' . $exception->getMessage());
    }
}
