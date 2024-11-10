<?php

namespace App\Livewire;

use App\LinkertScaleEnum;
use App\Models\Extracurricular;
use App\Models\StudentExtracurricular;
use App\Models\TeacherExtracurricular;
use Livewire\Component;

class LegerPreviewExtracurricular extends Component
{
    public $extracurricular;
    public $students;
    public $linkertScaleEnum;

    public function mount($extracurricular_id)
    {
        $this->extracurricular = TeacherExtracurricular::where('extracurricular_id', $extracurricular_id)->first();
        $this->students = StudentExtracurricular::where('extracurricular_id', $extracurricular_id)->get();
        $this->linkertScaleEnum = LinkertScaleEnum::class;
    }

    public function render()
    {
        return view('livewire.leger-preview-extracurricular');
    }
}
