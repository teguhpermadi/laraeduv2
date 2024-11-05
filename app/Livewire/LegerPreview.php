<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\TeacherSubject;
use App\Models\Leger;
use App\Models\LegerRecap;

class LegerPreview extends Component
{
    public $teacherSubject;
    public $students;
    public $competency_count;
    public $leger;
    public $legerRecap;

    public function mount($id)
    {
        $teacherSubject = TeacherSubject::with('subject', 'competency')->withCount('competency')->find($id);
        
        $this->legerRecap = LegerRecap::where('teacher_subject_id', $id)->first();
        

        // dd($this->competency->toArray());

        $this->teacherSubject = $teacherSubject;
        $this->competency_count = $teacherSubject->competency_count;
        $this->leger = Leger::where('teacher_subject_id', $id)->get();

        $data = collect();

        foreach ($this->leger as $leger) {
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

        // dd($data->toArray());

        // Urutkan data berdasarkan sum secara descending
        $data = $data->sortByDesc('sum')->values();

        // Tambahkan peringkat
        $data = $data->map(function ($item, $index) {
            $item['rank'] = $index + 1;
            return $item;
        });

        // Urutkan kembali berdasarkan student_id
        $data = $data->sortBy('student_id')->values();

        $this->students = $data;

    }

    public function render()
    {
        return view('livewire.leger-preview');
    }
} 