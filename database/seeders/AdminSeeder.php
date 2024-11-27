<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = User::firstOrCreate([
            'email' => 'superadmin@admin.com',
        ],[
            'name' => 'superadmin',
            'username' => 'superadmin',
            'email' => 'superadmin@admin.com',
            'password' => Hash::make('password')
        ]);

        $superAdmin->assignRole('super_admin');

        // buatkan user admin
        $admin = User::firstOrCreate([
            'email' => 'admin@admin.com',
        ],[
            'name' => 'admin',
            'username' => 'admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password')
        ]);

        $admin->assignRole('admin');
    }
}
