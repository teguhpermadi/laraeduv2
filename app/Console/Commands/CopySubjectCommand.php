<?php

namespace App\Console\Commands;

use App\Models\Subject;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CopySubjectCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:copy-subject';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $subjects = DB::connection('laraedu')->table('subjects')->get();

        foreach ($subjects as $subject) {
            $array = json_decode(json_encode($subject), true);

            $data = [
                'id' => $array['ulid'],
                'name' => $array['name'],
                'code' => $array['code'],
                'order' => $array['order'],
            ];

            try {
                Subject::create($data);
            } catch (\Throwable $th) {
                Log::error($th);
            }
        }
    }
}
