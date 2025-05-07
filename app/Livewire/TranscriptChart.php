<?php

namespace App\Livewire;

use Livewire\Component;

class TranscriptChart extends Component
{
    public $student, $transcript, $labels, $dataset1, $dataset2, $dataset3;

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

            $report_score = $transcript->report_score;
            $written_exam = $transcript->written_exam;
            $practical_exam = $transcript->practical_exam;
            
            $calculate_for_dataset1 = \round((($report_score + $written_exam + $practical_exam) / 3), 2);
            $calculate_for_dataset2 = \round((($report_score * 50 / 100) + ($written_exam * 30 / 100) + ($practical_exam * 20 / 100)), 2);
            $calculate_for_dataset3 = \round((($report_score * 60 / 100) + ($written_exam * 30 / 100) + ($practical_exam * 10 / 100)), 2);
            
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
