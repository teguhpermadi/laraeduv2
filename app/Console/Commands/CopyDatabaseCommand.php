<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\CopyAcademicYearJob;
use App\Jobs\CopyStudentJob;
use App\Jobs\CopyTeacherJob;
use Illuminate\Support\Facades\Bus;

class CopyDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:copy-database {--academic-year} {--teacher} {--student} {--all}';

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
        // switch case
        switch (true) {
            case $this->option('all'):
                // Menjalankan kedua job dengan antrian
                Bus::dispatch(new CopyAcademicYearJob());
                Bus::dispatch(new CopyTeacherJob());
                Bus::dispatch(new CopyStudentJob());
                break;

            case $this->option('academic-year'):
                // Menjalankan job CopyAcademicYearJob
                Bus::dispatch(new CopyAcademicYearJob());
                $this->info('Data academic year berhasil di copy');
                break;

            case $this->option('teacher'):
                // Menjalankan job CopyTeacherJob
                Bus::dispatch(new CopyTeacherJob());
                $this->info('Data teacher berhasil di copy');
                break;

            case $this->option('student'):
                // Menjalankan job CopyStudentJob
                Bus::dispatch(new CopyStudentJob());
                $this->info('Data student berhasil di copy');
                break;

            default:
                // buatkan pesan pilihan yang dapat inputkan pilihannya
                $this->info('Pilihan tidak valid');
                break;
        }
    }
}
