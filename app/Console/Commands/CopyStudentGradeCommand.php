<?php

namespace App\Console\Commands;

use App\Models\StudentGrade;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CopyStudentGradeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:copy-student-grade';

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
        $studentGrades = DB::connection('laraedu')->table('student_grade')->get();

        foreach ($studentGrades as $studentGrade) {
            $array = json_decode(json_encode($studentGrade), true);

            $data = [
                'id' => $array['ulid'],
                'academic_year_id' => $array['academic_year_ulid'],
                'student_id' => $array['student_ulid'],
                'grade_id' => $array['grade_ulid'],
            ];

            try {
                StudentGrade::create($data);
            } catch (\Throwable $th) {
                Log::error($th);
            }
        }
    }
}
