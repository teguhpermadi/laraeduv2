<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CopyAttendanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:copy-attendance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy attendance from laraedu to laraeduv2';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $attendances = DB::connection('laraedu')->table('attendances')->get();
        
        foreach ($attendances as $attendance) {
            $array = json_decode(json_encode($attendance), true);
            
            $data = [
                'id' => $array['ulid'],
                'academic_year_id' => $array['academic_year_ulid'],
                'grade_id' => $array['grade_ulid'],
                'student_id' => $array['student_ulid'],
                'sick' => $array['sick'],
                'permission' => $array['permission'],
                'absent' => $array['absent'],
                'note' => $array['note'],
                'achievement' => $array['achievement'],
                'status' => $array['status'],
            ];

            try {
                Attendance::create($data);
            } catch (\Throwable $th) {
                Log::error($th->getMessage());
            }
        }
    }
}
