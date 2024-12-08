<?php

namespace App\Livewire;

use App\Models\Leger;
use App\Models\Student;
use App\Models\TeacherGrade;
use App\Models\TeacherSubject;
use Livewire\Component;

class LegerPreviewMyGrade extends Component
{
    public $myGrade;
    public $subjects;

    public function mount()
    {
        $myGrade = TeacherGrade::myGrade()->mySubject()->get();
        $this->myGrade = $myGrade;
    }

    public function render()
    {
        return view('livewire.leger-preview-my-grade');
    }
}
