<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Role::updateOrCreate(['name' => 'admin']);
        $teacher = Role::updateOrCreate(['name' => 'teacher']);
        $teacherGrade = Role::updateOrCreate(['name' => 'teacher grade']);
        $teacherUmmi = Role::updateOrCreate(['name' => 'teacher ummi']);
    }
}
