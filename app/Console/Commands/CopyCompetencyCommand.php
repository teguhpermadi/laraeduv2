<?php

namespace App\Console\Commands;

use App\Models\Competency;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CopyCompetencyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:copy-competency';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy competency from laraedu to laraeduv2';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $competencies = DB::connection('laraedu')->table('competencies')->get();

        foreach ($competencies as $competency) {
            $array = json_decode(json_encode($competency), true);

            // $teacherSubject = $competency->teacher_subject_id;
            
            $data = [
                'id' => $array['ulid'],
                'teacher_subject_id' => $array['teacher_subject_ulid'],
                'code' => $array['code'],
                'description' => $array['description'],
                'passing_grade' => $array['passing_grade'],
                'half_semester' => $array['half_semester'],
                'code_skill' => $array['code_skill'],
                'description_skill' => $array['description_skill'],
            ];

            try {
                Competency::create($data);
            } catch (\Throwable $th) {
                Log::error($th);
            }
        }
    }
}
