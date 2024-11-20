<?php

namespace App\Observers;

use App\Filament\Pages\Leger;
use App\Models\Subject;

class SubjectObserver
{
    public function created(Subject $subject)
    {
        // $subject->order = $subject->id;
        // $subject->save();
    }

    public function updated(Subject $subject)
    {
        // update leger subject order
        Leger::where('subject_id', $subject->id)->update(['subject_order' => $subject->order]);
    }
}
