<?php

namespace App\Jobs;

use App\Enums\PhaseEnum;
use App\Models\Grade;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CopyGradeJob implements ShouldQueue
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
        $grades = DB::connection('laraedu')->table('grades')->get();

        foreach ($grades as $grade) {
            $fase = $grade->fase;
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
                'id' => $grade->id,
                'name' => $grade->name,
                'grade' => $grade->grade,
                'phase' => $phase,
            ];

            $array = json_decode(json_encode($data), true);

            Grade::create($array);
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('CopyGradeJob failed: ' . $exception->getMessage());
    }
}
