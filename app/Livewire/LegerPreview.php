<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TeacherSubject;

class LegerPreview extends Component
{
    public $teacherSubject;
    public $students;
    public $competency_count;

    public function mount($teacherSubject)
    {
        $competency = TeacherSubject::with('subject')->withCount('competency')->find($teacherSubject);
        $this->teacherSubject = $competency;
        $this->competency_count = $competency->competency_count;

        $competency_id = $competency->competency->pluck('id');

        $teacherSubject = TeacherSubject::with(['studentGrade.studentCompetency' => function ($query) use ($competency_id) {
            $query->whereIn('competency_id', $competency_id);
        }])->find($teacherSubject);

        $data = collect();

        foreach ($teacherSubject->studentGrade as $studentGrade) {

            $data[$studentGrade->student_id] = collect([
                'student_id' => $studentGrade->student_id,
                'student' => $studentGrade->student,
                'competency_count' => count($studentGrade->studentCompetency),
                'avg' => round($studentGrade->studentCompetency->avg('score'), 0),
                'sum' => $studentGrade->studentCompetency->sum('score'),
                'metadata' => $studentGrade->studentCompetency,
            ]);
        }

        // Sort dan tambah ranking
        $data = $data->sortByDesc('sum')
            ->values()
            ->map(function ($item, $index) {
                $item['rank'] = $index + 1;
                return $item;
            })
            ->sortBy('student_id')
            ->values();

        $this->students = $data;
    }

    public function render()
    {
        return view('livewire.leger-preview');
    }
} 