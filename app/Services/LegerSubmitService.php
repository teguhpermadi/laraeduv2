<?php

namespace App\Services;

use App\Enums\CategoryLegerEnum;
use App\Models\Leger as ModelsLeger;
use App\Models\LegerNote;
use App\Models\LegerRecap;
use App\Models\TeacherSubject;

class LegerSubmitService
{
    public function submit(array $data): void
    {
        $this->saveCategory($data['leger_full_semester'], $data, CategoryLegerEnum::FULL_SEMESTER->value);
        $this->saveCategory($data['leger_half_semester'], $data, CategoryLegerEnum::HALF_SEMESTER->value);
    }

    private function saveCategory(array $students, array $data, string $category): void
    {
        foreach ($students as $student) {
            $leger = ModelsLeger::updateOrCreate(
                [
                    'academic_year_id' => $data['academic_year_id'],
                    'student_id' => $student['student_id'],
                    'teacher_subject_id' => $data['teacher_subject_id'],
                    'teacher_id' => $student['teacher_id'],
                    'subject_id' => $student['subject_id'],
                    'category' => $category,
                ],
                [
                    'passing_grade' => $student['passing_grade'],
                    'score' => $student['avg_score'],
                    'score_skill' => $student['avg_skill'],
                    'sum' => $student['sum_score'],
                    'sum_skill' => $student['sum_skill'],
                    'rank' => $student['ranking'],
                    'description' => $student['description'],
                    'description_skill' => $student['description_skill'],
                    'metadata' => $student['competencies'],
                    'subject_order' => $student['subject_order'],
                ]
            );

            LegerNote::updateOrCreate(
                ['leger_id' => $leger->id],
                ['note' => '-']
            );
        }

        $recapData = [
            'academic_year_id' => $data['academic_year_id'],
            'teacher_subject_id' => $data['teacher_subject_id'],
            'category' => $category,
        ];

        $extra = match ($category) {
            CategoryLegerEnum::FULL_SEMESTER->value => ['updated_at' => $data['time_signature']],
            default => [],
        };

        LegerRecap::updateOrCreate($recapData, $extra);
    }

    public function syncTeacherSubject(TeacherSubject $record): void
    {
        $teacherSubject = TeacherSubject::with([
            'teacher',
            'subject',
            'academic',
            'competency',
            'studentGrade.studentCompetency.competency',
        ])->find($record->id);

        $calculator = app(LegerCalculationService::class);

        $competenciesFull = $teacherSubject->competency;
        $competenciesHalf = $teacherSubject->competency->where('half_semester', true);

        $studentsFull = $calculator->buildStudentsData(
            $teacherSubject->studentGrade,
            $competenciesFull,
            $teacherSubject,
            CategoryLegerEnum::FULL_SEMESTER->value
        );

        $studentsHalf = $calculator->buildStudentsData(
            $teacherSubject->studentGrade,
            $competenciesHalf,
            $teacherSubject,
            CategoryLegerEnum::HALF_SEMESTER->value
        );

        if ($calculator->hasNoScores($studentsFull)) {
            throw new \RuntimeException('Tidak ada nilai yang bisa disinkronisasi');
        }

        $this->submit([
            'leger_full_semester' => $studentsFull->toArray(),
            'leger_half_semester' => $studentsHalf->toArray(),
            'academic_year_id' => $teacherSubject->academic->id,
            'teacher_subject_id' => $teacherSubject->id,
            'time_signature' => now(),
        ]);
    }
}
