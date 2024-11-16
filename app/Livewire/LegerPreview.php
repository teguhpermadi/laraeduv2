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

    public function mount($id)
    {
        $teacherSubject = TeacherSubject::find($id);

        $this->teacherSubject = $teacherSubject;

        $this->legerRecapFullSemester = $teacherSubject->legerRecap->where('category', CategoryLegerEnum::FULL_SEMESTER->value)->first();
        $this->legerRecapHalfSemester = $teacherSubject->legerRecap->where('category', CategoryLegerEnum::HALF_SEMESTER->value)->first();
        
        $this->legerFullSemester = $teacherSubject->leger->where('category', CategoryLegerEnum::FULL_SEMESTER->value);
        $this->legerHalfSemester = $teacherSubject->leger->where('category', CategoryLegerEnum::HALF_SEMESTER->value);

        $this->competencyCountHalfSemester = $teacherSubject->competency->where('half_semester', 1)->count();
        $this->competencyCountFullSemester = $teacherSubject->competency->count();

        $this->competencyHalfSemester = $teacherSubject->competency->where('half_semester', 1);
        $this->competencyFullSemester = $teacherSubject->competency; 
    }

    public function render()
    {
        return view('livewire.leger-preview');
    }
} 