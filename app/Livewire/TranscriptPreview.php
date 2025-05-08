<?php

namespace App\Livewire;

use App\Models\Student;
use App\Models\Transcript;
use Livewire\Attributes\On;
use Livewire\Component;

class TranscriptPreview extends Component
{
    public $students;
    
    public function mount()
    {
        $students = Student::has('transcript')
            ->with(['transcript' => function ($query) {
                $query->orderBy('subject_id', 'asc');
            }])
            ->orderBy('id', 'asc')->get();
        $this->students = $students;
    }

    public function render()
    {
        return view('livewire.transcript-preview');
    }
}
