<?php

namespace App\Console\Commands;

use App\Models\Student;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CopyStudentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:copy-student';

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
        $students = DB::connection('laraedu')->table('students')->get();

        foreach ($students as $student) {
            $array = json_decode(json_encode($student), true);

            $data = [
                'id' => $array['ulid'],
                'nisn' => $array['nisn'],
                'nis' => $array['nis'],
                'name' => $array['name'],
                'gender' => $array['gender'],
                'active' => $array['active'],
                'city_born' => $array['city_born'],
                'birthday' => $array['birthday'],
                'nick_name' => $array['nick_name'],
            ];

            try {
                Student::create($data);
            } catch (\Throwable $th) {
                // kirim ke log
                Log::error($th);
            }
        }
    }
}
