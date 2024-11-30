<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CopyStudentJob implements ShouldQueue
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
        $students = DB::connection('laraedu')->table('students')->get();

        $data = [];
        foreach ($students as $student) {
            // atur ulang datanya
            $data[] = [
                'nisn' => $student->nisn,
                'nis' => $student->nis,
                'name' => $student->name,
                'gender' => $student->gender,
                'active' => $student->active,
                'city_born' => $student->city_born,
                'birthday' => $student->birthday,
                'nick_name' => $student->nick_name,
            ];

        }

        DB::connection('mysql')->table('students')->upsert($data, ['id']);
    }
}
