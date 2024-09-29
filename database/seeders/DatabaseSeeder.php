<?php

namespace Database\Seeders;

use App\Models\StudentGrade;
use App\Models\TeacherGrade;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminSeeder::class,
            AcademicYearSeeder::class,
            TeacherSeeder::class,
            StudentSeeder::class,
            GradeSeeder::class,
            TeacherGrade::class,
            StudentGrade::class,
            TeacherGradeSeeder::class,
            StudentGradeSeeder::class,
        ]);
    }
}
