<?php

namespace App\Console\Commands;

use App\Enums\CurriculumEnum;
use App\Models\TeacherGrade;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CopyTeacherGradeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:copy-teacher-grade';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy teacher grade from laraedu to laraeduv2';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $teacherGrades = DB::connection('laraedu')->table('teacher_grades')->get();

        foreach ($teacherGrades as $teacherGrade) {
            $array = json_decode(json_encode($teacherGrade), true);

            $curriculum = '';
            switch ($array['curriculum']) {
                case '2013':
                    $curriculum = CurriculumEnum::K13->value;
                    break;
                
                default:
                    $curriculum = CurriculumEnum::KURMER->value;
                    break;
            }

            $data = [
                'id' => $array['ulid'],
                'academic_year_id' => $array['academic_year_ulid'],
                'teacher_id' => $array['teacher_ulid'],
                'grade_id' => $array['grade_ulid'],
                'curriculum' => $curriculum,
            ];

            try {
                TeacherGrade::updateOrCreate($data);
            } catch (\Throwable $th) {
                Log::error($th);
            }
        }
    }
}
