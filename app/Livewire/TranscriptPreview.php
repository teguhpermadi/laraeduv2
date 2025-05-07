<?php

namespace App\Livewire;

use App\Models\Student;
use App\Models\Transcript;
use Livewire\Component;

class TranscriptPreview extends Component
{
    public $students;

    public function mount()
    {
        $students = Student::has('transcript')->with('transcript')->get();
        $this->students = $students;
    }


    public function render()
    {
        return view('livewire.transcript-preview');
    }
}
