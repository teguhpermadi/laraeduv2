<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class TranscriptChart extends Component
{
    public $student, $transcript, $labels, $dataset1, $dataset2, $dataset3, $weights;
    
    public function mount($student)
    {
        $this->student = $student;
        $transcripts = $student->transcript;

        $labels = [];
        $dataset1 = [];
        $dataset2 = [];
        $dataset3 = [];

        foreach ($transcripts as $transcript) {
            $labels[] = [
                $transcript->subject->code
            ];

            $calculate_for_dataset1 = $transcript->calculateAverage();
            $calculate_for_dataset2 = $transcript->calculateAverage();
            $calculate_for_dataset3 = $transcript->calculateAverage();
            
            $dataset1[] = $calculate_for_dataset1;
            $dataset2[] = $calculate_for_dataset2;
            $dataset3[] = $calculate_for_dataset3;
        }

        // \dd($dataset1);

        $this->labels = $labels;
        $this->dataset1 = $dataset1;
        $this->dataset2 = $dataset2;
        $this->dataset3 = $dataset3;

    }

    public function render()
    {
        return view('livewire.transcript-chart');
    }
}
