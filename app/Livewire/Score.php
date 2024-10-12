<?php

namespace App\Livewire;

use App\Models\Competency;
use App\Models\StudentCompetency;
use Livewire\Component;

class Score extends Component
{
    public $score, $color;

    public function mount($student_id, $competency_id, $passing_grade, $column = 'score')
    {
        $data = StudentCompetency::where('student_id', $student_id)
            ->where('competency_id', $competency_id)->first();
        
        // $competency = Competency::find($competency_id);
        
        $this->score = ($data) ? $data->score : 'null';
        $this->color = ($this->score < $passing_grade) ? 'yellow' : '';
    }
    
    public function render()
    {
        return view('livewire.score');
    }
}
