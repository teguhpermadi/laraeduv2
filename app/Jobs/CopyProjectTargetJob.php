<?php

namespace App\Jobs;

use App\Enums\PhaseEnum;
use App\Models\ProjectTarget;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CopyProjectTargetJob implements ShouldQueue
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
        $projectTargets = DB::connection('laraedu')->table('project_targets')->get();

        foreach ($projectTargets as $projectTarget) {
            $fase = $projectTarget->phase;
            $phase = '';

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
                'project_id' => $projectTarget->project_id,
                'phase' => $phase,
                'dimention_id' => $projectTarget->dimention_id,
                'element_id' => $projectTarget->element_id,
                'sub_element_id' => $projectTarget->sub_element_id,
                'value_id' => $projectTarget->value_id,
                'sub_value_id' => $projectTarget->sub_value_id,
                'target_id' => $projectTarget->target_id,
            ];

            ProjectTarget::create($data);
        }
    }
}
