<?php

namespace App\Services;

use App\Models\TeacherSubject;
use Illuminate\Support\Collection;

class LegerCalculationService
{
    public function __construct(
        private DescriptionService $descriptionService
    ) {}

    public function buildStudentsData(Collection $studentGrades, Collection $competencies, TeacherSubject $teacherSubject, string $category): Collection
    {
        $teacherId = $teacherSubject->teacher_id;
        $subjectId = $teacherSubject->subject_id;
        $subjectOrder = $teacherSubject->subject->order;

        $students = $studentGrades->map(function ($student) use ($competencies, $subjectOrder, $teacherId, $subjectId, $teacherSubject, $category) {
            $filteredCompetencies = $student->studentCompetency
                ->whereIn('competency_id', $competencies->pluck('id'));

            $result = $teacherSubject->calculateLegerScore($filteredCompetencies, $category);
            $description = $this->descriptionService->getDescription($filteredCompetencies);

            return [
                'student_id' => $student->student->id,
                'teacher_id' => $teacherId,
                'subject_id' => $subjectId,
                'subject_order' => $subjectOrder,
                'nis' => $student->student->nis,
                'name' => $student->student->name,
                'avg_score' => round($result['avg_score'], 0),
                'avg_skill' => round($result['avg_skill'], 0),
                'sum_score' => $filteredCompetencies->sum('score'),
                'sum_skill' => $filteredCompetencies->sum('score_skill'),
                'description' => $description['description'],
                'description_skill' => $description['description_skill'],
                'competency_count' => $competencies->count(),
                'passing_grade' => round($competencies->avg('passing_grade'), 0),
                'competencies' => $filteredCompetencies
                    ->sortBy('competency_id')
                    ->map(fn ($c) => [
                        'competency_id' => $c->competency_id,
                        'code' => $c->competency->code,
                        'score' => $c->score,
                        'passing_grade' => $c->competency->passing_grade,
                        'description' => $c->competency->description,
                        'score_skill' => $c->score_skill,
                        'description_skill' => $c->competency->description_skill,
                    ]),
            ];
        });

        return $this->applyRanking($students);
    }

    private function applyRanking(Collection $students): Collection
    {
        return $students
            ->sortByDesc('sum_score')
            ->values()
            ->map(fn ($item, $index) => [...$item, 'ranking' => $index + 1])
            ->sortBy('student_id');
    }

    public function hasNoScores(Collection $students): bool
    {
        return $students->contains(fn ($item) => empty($item['competencies']));
    }
}
