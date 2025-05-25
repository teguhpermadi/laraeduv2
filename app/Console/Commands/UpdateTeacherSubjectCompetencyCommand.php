<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\UpdateTeacherSubjectCompetencyJob;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class UpdateTeacherSubjectCompetencyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-exam-score';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate exam score for each teacher subject';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $academicYearId = $this->ask('Masukkan academic year ID');
        Bus::dispatch(new UpdateTeacherSubjectCompetencyJob($academicYearId));
        $this->info('Job UpdateTeacherSubjectCompetencyJob telah dijalankan');
    }
}
