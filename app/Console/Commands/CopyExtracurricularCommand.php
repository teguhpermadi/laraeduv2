<?php

namespace App\Console\Commands;

use App\Models\Extracurricular;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CopyExtracurricularCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:copy-extracurricular';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy extracurricular from laraedu to laraeduv2';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $extracurriculars = DB::connection('laraedu')->table('extracurriculars')->get();

        foreach ($extracurriculars as $extracurricular) {
            $array = json_decode(json_encode($extracurricular), true);

            $data = [
                'id' => $array['ulid'],
                'name' => $array['name'],
            ];

            try {
                //code...
                Extracurricular::create($data);
            } catch (\Throwable $th) {
                //throw $th;
                Log::error($th);
            }
        }
        
    }
}
