<?php

namespace Database\Seeders;

use App\Models\Competency;
use App\Models\StudentCompetency;
use App\Models\TeacherSubject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentCompetencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // get students
        $studentGrades = TeacherSubject::with('studentGrade')->get();

        foreach ($studentGrades as $studentGrade) {

            $students = $studentGrade->studentGrade->pluck('student_id');

            // get competency id from teacher subject
            $competencies = Competency::where('teacher_subject_id', $studentGrade->id)->get()->pluck('id');

            // create new student competency
            // $data = [];
            foreach ($students as $student) {
                foreach ($competencies as $competency) {
                    $data = [
                        'teacher_subject_id' => $studentGrade->id,
                        'student_id' => $student,
                        'competency_id' => $competency,
                        // 'created_at' => now(),
                    ];

                    // update or create
                    StudentCompetency::updateOrCreate($data, ['score' => fake()->numberBetween(50, 100)]);
                }
            }
        }
    }
}
