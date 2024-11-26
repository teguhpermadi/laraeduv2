<?php

namespace App\Livewire;

use App\Models\LegerQuran;
use App\Models\LegerQuranRecap;
use App\Models\Scopes\AcademicYearScope;
use App\Models\TeacherQuranGrade;
use Livewire\Component;

class LegerPreviewQuran extends Component
{
    public $legerQuran;
    public $teacherQuranGrade;
    public $legerQuranRecap;
    public $competency_count;
    public $competencies;
    public $students;
    public $studentsWithNotes;

    public function mount($id)
    {
        $teacherQuranGrade = TeacherQuranGrade::find($id);
        $this->teacherQuranGrade = $teacherQuranGrade;

        $this->legerQuran = $teacherQuranGrade->legerQuran;

        $this->competency_count = $teacherQuranGrade->competencyQuran->count();
        $this->competencies = $teacherQuranGrade->competencyQuran;
        // dd($this->competency->toArray());

        // leger quran recap
        $this->legerQuranRecap = $teacherQuranGrade->legerQuranRecap;
        // dd($this->legerQuranRecap->toArray());

        // leger quran students
        $this->students = $teacherQuranGrade->legerQuran;
        // dd($this->students->toArray());

        // leger quran note
        $this->studentsWithNotes = $teacherQuranGrade->legerQuran()
            ->with(['student', 'quranNote'])
            ->get()
            ->map(function ($leger) {
                return [
                    'nis' => $leger->student->nis,
                    'name' => $leger->student->name,
                    'note' => $leger->quranNote->note,
                ];
            });

        // dd($this->studentsWithNotes->toArray());
    }

    public function render()
    {
        return view('livewire.leger-preview-quran');
    }
}
