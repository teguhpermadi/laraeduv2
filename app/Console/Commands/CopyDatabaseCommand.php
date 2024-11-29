<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\CopyAcademicYearJob;
use Illuminate\Support\Facades\Bus;

class CopyDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:copy-database {--academic-year}';

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
        if ($this->option('academic-year')) {
            // Menjalankan job dengan antrian
            Bus::dispatch(new CopyAcademicYearJob());
            $this->info('Job CopyAcademicYearJob telah dimasukkan ke dalam antrian.');
        } else {
            // Menjalankan job secara langsung
            (new CopyAcademicYearJob())->handle();
            $this->info('Job CopyAcademicYearJob telah dijalankan secara langsung.');
        }
    }
}
