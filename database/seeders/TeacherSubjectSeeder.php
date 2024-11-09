<?php

namespace Database\Seeders;

use App\Models\TeacherSubject;
use App\Models\Competency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeacherSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat data factory
        $data = TeacherSubject::factory(20)->make()->toArray();

        // Tambahkan satu per satu agar observer berjalan
        foreach ($data as $item) {
            // gunakan updateOrCreate agar observer berjalan
            TeacherSubject::updateOrCreate(
                [
                    'academic_year_id' => $item['academic_year_id'],
                    'grade_id' => $item['grade_id'],
                    'subject_id' => $item['subject_id'],
                ],
                [
                    'teacher_id' => $item['teacher_id'],
                    'time_allocation' => $item['time_allocation'],
                ]
            );
        }

        // Tambahkan kompetensi untuk setiap teacher subject
        TeacherSubject::all()->each(function ($teacherSubject) {
            // 2 kompetensi dengan half_semester true
            for ($i = 1; $i <= 2; $i++) {
                Competency::create([
                    'teacher_subject_id' => $teacherSubject->id,
                    'code' => 'KD' . $i . '-S1',
                    'description' => 'Kompetensi Dasar ' . $i . ' Semester 1',
                    'passing_grade' => 75,
                    'half_semester' => true,
                ]);
            }

            // 2 kompetensi dengan half_semester false
            for ($i = 1; $i <= 2; $i++) {
                Competency::create([
                    'teacher_subject_id' => $teacherSubject->id,
                    'code' => 'KD' . $i . '-S2',
                    'description' => 'Kompetensi Dasar ' . $i . ' Semester 2',
                    'passing_grade' => 75,
                    'half_semester' => false,
                ]);
            }
        });
    }
}
