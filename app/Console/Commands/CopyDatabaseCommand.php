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
use App\Jobs\CopyUserableJob;
use App\Jobs\CopyUserJob;
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
        Bus::dispatch(new CopyTeacherJob());
        Bus::dispatch(new CopyStudentJob());
        Bus::dispatch(new CopyDataStudentJob());
        Bus::dispatch(new CopyGradeJob());
        Bus::dispatch(new CopySubjectJob());
        Bus::dispatch(new CopyExtracurricularJob());
        Bus::dispatch(new CopyAcademicYearJob());
        // Bus::dispatch(new CopyUserJob());
        // Bus::dispatch(new CopyUserableJob());
        // Bus::dispatch(new CopyTeacherGradeJob());
        // Bus::dispatch(new CopyTeacherSubjectJob());
        // Bus::dispatch(new CopyStudentExtracurricularJob());
        // Bus::dispatch(new CopyTeacherExtracurricularJob());
        // Bus::dispatch(new CopyCompetencyJob());
        // Bus::dispatch(new CopyStudentCompetencyJob());
        // Bus::dispatch(new CopyProjectCoordinatorJob());
        // Bus::dispatch(new CopyProjectJob());
        // Bus::dispatch(new CopyProjectTargetJob());
        // Bus::dispatch(new CopyProjectNoteJob());
        // Bus::dispatch(new CopyProjectStudentJob());

        // Bus::dispatch(new CopyDataStudentJob());
    }
}
