<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\ProjectCoordinator;
use App\Models\Scopes\AcademicYearScope;
use App\Models\TeacherGrade;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectCoordinatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coordinators = TeacherGrade::withoutGlobalScope(AcademicYearScope::class)->get();
        
        foreach ($coordinators as $coordinator) {
            ProjectCoordinator::create([
                'academic_year_id' => AcademicYear::first()->id,
                'teacher_id' => $coordinator->teacher_id,
                'grade_id' => $coordinator->grade_id,
            ]);
        }
    }
}
