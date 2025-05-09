<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;
use App\Settings\TranscriptWeight;

class TranscriptChart extends Component
{
    public $student, $transcript, $labels, $dataset1, $dataset2;
    public $weight_report1, $weight_written_exam1, $weight_practical_exam1, $weight_report2, $weight_written_exam2, $weight_practical_exam2;
    
    public function mount($student)
    {
        $this->student = $student;
        $transcripts = $student->transcript;

        $labels = [];
        $dataset1 = [];
        $dataset2 = [];

        $transcriptWeight = app(TranscriptWeight::class);
        $weight_report1 = $transcriptWeight->weight_report1;
        $weight_written_exam1 = $transcriptWeight->weight_written_exam1;
        $weight_practical_exam1 = $transcriptWeight->weight_practical_exam1;
        $weight_report2 = $transcriptWeight->weight_report2;
        $weight_written_exam2 = $transcriptWeight->weight_written_exam2;
        $weight_practical_exam2 = $transcriptWeight->weight_practical_exam2;

        $this->weight_report1 = $weight_report1;
        $this->weight_written_exam1 = $weight_written_exam1;
        $this->weight_practical_exam1 = $weight_practical_exam1;
        $this->weight_report2 = $weight_report2;
        $this->weight_written_exam2 = $weight_written_exam2;
        $this->weight_practical_exam2 = $weight_practical_exam2;
        
        foreach ($transcripts as $transcript) {
            $labels[] = [
                $transcript->subject->code
            ];

            $calculate_for_dataset1 = $transcript->calculateAverage($weight_report1, $weight_written_exam1, $weight_practical_exam1);
            $calculate_for_dataset2 = $transcript->calculateAverage($weight_report2, $weight_written_exam2, $weight_practical_exam2);
            
            $dataset1[] = $calculate_for_dataset1;
            $dataset2[] = $calculate_for_dataset2;
        }

        // \dd($dataset1);

        $this->labels = $labels;
        $this->dataset1 = $dataset1;
        $this->dataset2 = $dataset2;

    }

    public function render()
    {
        return view('livewire.transcript-chart');
    }
}
