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
            TeacherSeeder::class,
            StudentSeeder::class,
            AcademicYearSeeder::class,
            GradeSeeder::class,
            TeacherGradeSeeder::class,
            StudentGradeSeeder::class,
            TeacherGradeSeeder::class,
            StudentGradeSeeder::class,
            SubjectSeeder::class,
            TeacherSubjectSeeder::class,
            CompetencySeeder::class,
            StudentCompetencySeeder::class,

            // Extracurricular
            ExtracurricularSeeder::class,
            TeacherExtracurricularSeeder::class,
            StudentExtracurricularSeeder::class,

            // Project
            DimentionSeeder::class,
            ElementSeeder::class,
            SubElementSeeder::class,
            TargetSeeder::class,
            ValueSeeder::class,
            SubValueSeeder::class,
            ProjectThemeSeeder::class,
            ProjectCoordinatorSeeder::class,
            ProjectSeeder::class,
            ProjectTargetSeeder::class,
            StudentProjectSeeder::class,

            // Quran
            QuranGradeSeeder::class,
            TeacherQuranGradeSeeder::class,
            StudentQuranGradeSeeder::class,
            CompetencyQuranSeeder::class,
        ]);
    }
}
