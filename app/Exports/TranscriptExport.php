<?php

namespace App\Exports;

use App\Models\Student;
use App\Models\Transcript;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TranscriptExport implements FromView
{
    public function view(): View
    {
        $students = Student::whereHas('transcript')->get();
        $subjects = Transcript::select('subject_id')->distinct()->get();
        $subjects->sortBy('subject_id');

        return view('components.exports.transcript', [
            'students' => $students,
            'subjects' => $subjects
        ]);
    }
}
