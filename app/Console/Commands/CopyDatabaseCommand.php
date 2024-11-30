<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\CopyAcademicYearJob;
use App\Jobs\CopyCompetencyJob;
use App\Jobs\CopyDataStudentJob;
use App\Jobs\CopyExtracurricularJob;
use App\Jobs\CopyGradeJob;
use App\Jobs\CopyProjectCoordinatorJob;
use App\Jobs\CopyProjectJob;
use App\Jobs\CopyProjectNoteJob;
use App\Jobs\CopyProjectStudentJob;
use App\Jobs\CopyProjectTargetJob;
use App\Jobs\CopyStudentCompetencyJob;
use App\Jobs\CopyStudentExtracurricularJob;
use App\Jobs\CopyStudentJob;
use App\Jobs\CopySubjectJob;
use App\Jobs\CopyTeacherExtracurricularJob;
use App\Jobs\CopyTeacherGradeJob;
use App\Jobs\CopyTeacherJob;
use App\Jobs\CopyTeacherSubjectJob;
use Illuminate\Support\Facades\Bus;

class CopyDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:copy-database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy database from laraedu to laraeduv2';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Bus::chain([
            new CopyTeacherJob(),
            new CopyStudentJob(),
            new CopyDataStudentJob(),
            new CopyGradeJob(),
            new CopySubjectJob(),
            new CopyExtracurricularJob(),
            new CopyAcademicYearJob(),
            new CopyTeacherGradeJob(),
            new CopyTeacherSubjectJob(),
            new CopyStudentExtracurricularJob(),
            new CopyTeacherExtracurricularJob(),
            new CopyCompetencyJob(),
            new CopyStudentCompetencyJob(),
            new CopyProjectCoordinatorJob(),
            new CopyProjectJob(),
            new CopyProjectTargetJob(),
            new CopyProjectNoteJob(),
            new CopyProjectStudentJob(),
        ])->dispatch();
    }
}
