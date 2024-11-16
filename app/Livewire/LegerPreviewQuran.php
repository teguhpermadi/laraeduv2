<?php

namespace App\Livewire;

use App\Models\LegerQuran;
use App\Models\LegerQuranRecap;
use App\Models\Scopes\AcademicYearScope;
use App\Models\TeacherQuranGrade;
use Livewire\Component;

class LegerPreviewQuran extends Component
{
    public $legerQuran;
    public $teacherQuranGrade;
    public $competency_count;
    public $students;


    public function mount($id)
    {
        $this->teacherQuranGrade = TeacherQuranGrade::withoutGlobalScope(AcademicYearScope::class)->with('competencyQuran')->find($id);

        $this->competency_count = $this->teacherQuranGrade->competencyQuran->count();

        $this->legerQuran = LegerQuranRecap::where('teacher_quran_grade_id', $id)->first();
        
        $this->students = $this->teacherQuranGrade->studentQuranGrade;

        $data = collect();

        foreach ($this->legerQuran->leger as $leger) {
            $metadata = $leger->metadata;

            $data[] = collect([
                'academic_year_id' => $leger->academic_year_id,
                'teacher_subject_id' => $leger->teacher_subject_id,
                'student_id' => $leger->student_id,
                'student' => $leger->student,
                'competency_count' => count($metadata),
                'avg' => $leger->score,
                'sum' => $leger->sum,
                'rank' => $leger->rank,
                'metadata' => $metadata,
                'description' => $leger->description    
            ]);
        }

        // Urutkan data berdasarkan sum secara descending
        $data = $data->sortByDesc('sum')->values();

        // Tambahkan peringkat
        $data = $data->map(function ($item, $index) {
            $item['rank'] = $index + 1;
            return $item;
        });

        // dd($data);

        // Urutkan kembali berdasarkan student_id
        $data = $data->sortBy('student_id')->values();

        $this->students = $data;
    }

    public function render()
    {
        return view('livewire.leger-preview-quran');
    }
}
