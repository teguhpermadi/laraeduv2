<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CopyDbCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:copy-db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy all database from laraedu to laraeduv2';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // migrate fresh
        Artisan::call('migrate:fresh');
        $this->info('Migrate fresh success');

        // db seed --class=ShieldSeeder
        Artisan::call('db:seed --class=ShieldSeeder');
        $this->info('Shield seed success');

        // handle copy teacher
        Artisan::call('app:copy-teacher');
        // tampilkan pesan success
        $this->info('Copy teacher success');

        // handle copy student
        Artisan::call('app:copy-student');
        $this->info('Copy student success');

        // handle grade
        Artisan::call('app:copy-grade');
        $this->info('Copy grade success');

        // handle subject
        Artisan::call('app:copy-subject');
        $this->info('Copy subject success');

        // handle extracurricular
        Artisan::call('app:copy-extracurricular');
        $this->info('Copy extracurricular success');

        // handle user
        Artisan::call('app:copy-user');
        $this->info('Copy user success');

        // handle give role to user
        Artisan::call('app:give-role-to-user');
        $this->info('Give role to user success');

        // handle academic year
        Artisan::call('app:copy-academic-year');
        $this->info('Copy academic year success');

        // handle teacher grade
        Artisan::call('app:copy-teacher-grade');
        $this->info('Copy teacher grade success');

        // handle student grade
        Artisan::call('app:copy-student-grade');
        $this->info('Copy student grade success');

        // handle teacher subject
        Artisan::call('app:copy-teacher-subject');
        $this->info('Copy teacher subject success');
    }
}
