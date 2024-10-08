<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            TeacherGradeSeeder::class,
            StudentGradeSeeder::class,
            TeacherGradeSeeder::class,
            StudentGradeSeeder::class,
            SubjectSeeder::class,
            TeacherSubjectSeeder::class,
            CompetencySeeder::class,
        ]);
    }
}
