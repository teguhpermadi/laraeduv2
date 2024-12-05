<?php

namespace App\Console\Commands;

use App\Enums\CategoryLegerEnum;
use App\Models\Competency;
use App\Models\StudentCompetency;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CopyStudentCompetencyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:copy-student-competency';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy student competency from laraedu to laraedu_new';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $studentCompetencies = DB::connection('laraedu')->table('student_competencies')->get();

        // update score dan score_skill setiap student competency
        foreach ($studentCompetencies as $studentCompetency) {
            $array = json_decode(json_encode($studentCompetency), true);

            // cari student competency berdasarkan teacher_subject_id, competency_id, dan student_id
            $studentCompetency = StudentCompetency::where('teacher_subject_id', $array['teacher_subject_ulid'])
                ->where('competency_id', $array['competency_ulid'])
                ->where('student_id', $array['student_ulid'])
                ->first();

            if ($studentCompetency) {
                // update score dan score_skill
                $studentCompetency->update([
                    'score' => $array['score'],
                    'score_skill' => $array['score_skill'],
                ]);
            }
        }

        // exam
        $exams = DB::connection('laraedu')->table('exams')->get();

        foreach ($exams as $exam) {
            $array = json_decode(json_encode($exam), true);

            // cari competency berdasarkan teacher_subject_id, competency_id, dan student_id
            $competencyHalfSemester = Competency::where('teacher_subject_id', $array['teacher_subject_ulid'])
                ->where('code', CategoryLegerEnum::HALF_SEMESTER->value)
                ->first();

            $competencyFullSemester = Competency::where('teacher_subject_id', $array['teacher_subject_ulid'])
                ->where('code', CategoryLegerEnum::FULL_SEMESTER->value)
                ->first();

            // cari student competency berdasarkan competencyHalfSemester
            $studentCompetencyHalfSemester = StudentCompetency::where('teacher_subject_id', $array['teacher_subject_ulid'])
                ->where('competency_id', $competencyHalfSemester->id)
                ->first();

            $studentCompetencyFullSemester = StudentCompetency::where('teacher_subject_id', $array['teacher_subject_ulid'])
                ->where('competency_id', $competencyFullSemester->id)
                ->first();

            // update score exam half semester
            $studentCompetencyHalfSemester->update([
                'score' => $array['score_middle'],
            ]);

            // update score exam full semester
            $studentCompetencyFullSemester->update([
                'score' => $array['score_last']
            ]);
        }
    }
}
