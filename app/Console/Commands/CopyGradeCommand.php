<?php

namespace App\Console\Commands;

use App\Enums\PhaseEnum;
use App\Models\Grade;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CopyGradeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:copy-grade';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy grade from laraedu to laraeduv2';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $grades = DB::connection('laraedu')->table('grades')->get();

        foreach ($grades as $grade) {
            $array = json_decode(json_encode($grade), true);
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
                'id' => $array['ulid'],
                'name' => $array['name'],
                'grade' => $array['grade'],
                'phase' => $phase,
                // 'is_inclusive' => $array['is_inclusive'],
            ];

            try {
                Grade::create($data);
            } catch (\Throwable $th) {
                Log::error($th);
            }
        }
    }
}
