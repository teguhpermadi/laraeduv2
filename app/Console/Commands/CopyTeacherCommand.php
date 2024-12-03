<?php

namespace App\Console\Commands;

use App\Models\Teacher;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CopyTeacherCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:copy-teacher';

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
        $teachers = DB::connection('laraedu')->table('teachers')->get();

        foreach ($teachers as $teacher) {
            $array = json_decode(json_encode($teacher), true);

            $data = [
                'id' => $array['ulid'],
                'name' => $array['name'],
                'gender' => $array['gender'],
                // 'signature' => $array['signature'],
                // 'nip' => $array['nip'],
                // 'nuptk' => $array['nuptk'],
                // 'photo' => $array['photo'],
            ];

            try {
                Teacher::create($data);
            } catch (\Throwable $th) {
                Log::error($th);
            }
        }
    }
}
