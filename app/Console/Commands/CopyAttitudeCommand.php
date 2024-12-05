<?php

namespace App\Console\Commands;

use App\Enums\LinkertScaleEnum;
use App\Models\Attitude;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CopyAttitudeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:copy-attitude';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy database attitude from laraedu to laraeudv2';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $attitudes = DB::connection('laraedu')->table('attitudes')->get();

        foreach ($attitudes as $attitude) {
            $array = json_decode(json_encode($attitude), true);

            switch ($array['attitude_religius']) {
                case 'amat baik':
                    $attitude_religius = LinkertScaleEnum::AMAT_BAIK->value;
                    break;

                case 'baik':
                    $attitude_religius = LinkertScaleEnum::BAIK->value;
                    break;

                case 'cukup baik':
                    $attitude_religius = LinkertScaleEnum::CUKUP->value;
                    break;

                case 'kurang baik':
                    $attitude_religius = LinkertScaleEnum::KURANG->value;
                    break;

                default:
                    $attitude_religius = LinkertScaleEnum::AMAT_KURANG->value;
                    break;
            }

            switch ($array['attitude_social']) {
                case 'amat baik':
                    $attitude_social = LinkertScaleEnum::AMAT_BAIK->value;
                    break;

                case 'baik':
                    $attitude_social = LinkertScaleEnum::BAIK->value;
                    break;

                case 'cukup baik':
                    $attitude_social = LinkertScaleEnum::CUKUP->value;
                    break;

                case 'kurang baik':
                    $attitude_social = LinkertScaleEnum::KURANG->value;
                    break;

                default:
                    $attitude_social = LinkertScaleEnum::AMAT_KURANG->value;
                    break;
            }

            $data = [
                'id' => $array['ulid'],
                'academic_year_id' => $array['academic_year_ulid'],
                'grade_id' => $array['grade_ulid'],
                'student_id' => $array['student_ulid'],
                'attitude_religius' => $attitude_religius,
                'attitude_social' => $attitude_social,
            ];

            try {
                Attitude::create($data);
            } catch (\Throwable $th) {
                Log::error($th->getMessage());
            }
        }
    }
}
