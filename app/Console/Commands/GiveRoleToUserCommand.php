<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class GiveRoleToUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:give-role-to-user-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Give role to user (super admin, admin, teacher, student)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // dapatkan user admin
        $admin = User::where('email', 'admin@admin.com')->first();

        try {
            $admin->assignRole('admin');
        } catch (\Throwable $th) {
            $this->error('Give role to user admin failed');
        }

        // buat user super admin
        $superAdmin = User::firstOrCreate([
            'email' => 'superadmin@admin.com',
        ],[
            'name' => 'superadmin',
            'username' => 'superadmin',
            'password' => Hash::make('password')
        ]);

        try {
            $superAdmin->assignRole('super_admin');
        } catch (\Throwable $th) {
            $this->error('Give role to user super admin failed');
        }
    }
}
