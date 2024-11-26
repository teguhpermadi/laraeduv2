<?php

namespace App\Livewire;

use App\Enums\CategoryLegerEnum;
use Livewire\Component;
use App\Models\TeacherSubject;
use App\Models\Leger;
use App\Models\LegerRecap;

class LegerPreview extends Component
{
    public $teacherSubject;
    public $competencyFullSemester;
    public $competencyHalfSemester;
    public $competencyCountFullSemester;
    public $competencyCountHalfSemester;
    public $legerFullSemester;
    public $legerHalfSemester;
    public $legerRecapFullSemester;
    public $legerRecapHalfSemester;
    public $studentsWithNotesFullSemester;
    public $studentsWithNotesHalfSemester;

    public function mount($id)
    {
        $teacherSubject = TeacherSubject::find($id);

        $this->teacherSubject = $teacherSubject;

        $this->legerRecapFullSemester = $teacherSubject->legerRecap->where('category', CategoryLegerEnum::FULL_SEMESTER->value)->first();
        $this->legerRecapHalfSemester = $teacherSubject->legerRecap->where('category', CategoryLegerEnum::HALF_SEMESTER->value)->first();
        
        $this->legerFullSemester = $teacherSubject->leger()->where('category', CategoryLegerEnum::FULL_SEMESTER->value)->get();
        $this->legerHalfSemester = $teacherSubject->leger()->where('category', CategoryLegerEnum::HALF_SEMESTER->value)->get();
        
        $this->competencyCountHalfSemester = $teacherSubject->competency->where('half_semester', 1)->count();
        $this->competencyCountFullSemester = $teacherSubject->competency->count();

        $this->competencyHalfSemester = $teacherSubject->competency->where('half_semester', 1);
        $this->competencyFullSemester = $teacherSubject->competency; 

        // leger note full semester
        $studentsWithNotesFullSemester = $this->teacherSubject->leger()
            ->with(['student', 'note'])
            ->where('category', CategoryLegerEnum::FULL_SEMESTER->value)
            ->get()
            ->map(function ($leger) {
                return [
                    'nis' => $leger->student->nis,
                    'name' => $leger->student->name,
                    'note' => $leger->note ? $leger->note->note : '-',
                ];
            });

        // leger note half semester 
        $studentsWithNotesHalfSemester = $this->teacherSubject->leger()
            ->with(['student', 'note'])
            ->where('category', CategoryLegerEnum::HALF_SEMESTER->value)
            ->get()
            ->map(function ($leger) {
                return [
                    'nis' => $leger->student->nis,
                    'name' => $leger->student->name,
                    'note' => $leger->note ? $leger->note->note : '-',
                ];
            });

        $this->studentsWithNotesHalfSemester = $studentsWithNotesHalfSemester;
        $this->studentsWithNotesFullSemester = $studentsWithNotesFullSemester;
    }

    public function render()
    {
        return view('livewire.leger-preview');
    }
} 