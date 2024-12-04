<?php

namespace App\Console\Commands;

use App\Models\Competency;
use App\Models\StudentCompetency;
use App\Models\TeacherSubject;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CopyTeacherSubjectCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:copy-teacher-subject';

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
        $this->delete();

        $teacherSubjects = DB::connection('laraedu')->table('teacher_subjects')->get();

        foreach ($teacherSubjects as $teacherSubject) {
            $array = json_decode(json_encode($teacherSubject), true);

            $data = [
                'id' => $array['ulid'],
                'academic_year_id' => $array['academic_year_ulid'],
                'teacher_id' => $array['teacher_ulid'],
                'subject_id' => $array['subject_ulid'],
                'grade_id' => $array['grade_ulid'],
                // 'time_allocation' => $array['time_allocation'],
            ];

            try {
                TeacherSubject::create($data);
            } catch (\Throwable $th) {
                Log::error($th);
            }
        }
    }

    public function delete()
    {
        // truncate table student_competency
        try {
            StudentCompetency::query()->delete();
        } catch (\Throwable $th) {
            Log::error($th);
        }

        // truncate table competency
        try {
            Competency::query()->delete();
        } catch (\Throwable $th) {
            Log::error($th);
        }

        // truncate table teacher_subject
        try {
            TeacherSubject::query()->delete();
        } catch (\Throwable $th) {
            Log::error($th);
        }
    }
}
