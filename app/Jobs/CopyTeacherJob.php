<?php

namespace App\Jobs;

use App\Models\Teacher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CopyTeacherJob implements ShouldQueue
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
        $teachers = DB::connection('laraedu')->table('teachers')->get();

        $data = [];
        foreach ($teachers as $teacher) {
            // atur ulang datanya
            $data[] = [
                'name' => $teacher->name,
                'gender' => $teacher->gender,
                'signature' => null,
                'nip' => null,
                'nuptk' => null,
                'photo' => null,
            ];
            
        }

        DB::connection('mysql')->table('teachers')->upsert($data, ['id']);
    }
}
