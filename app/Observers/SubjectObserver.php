<?php

namespace App\Observers;

use App\Filament\Pages\Leger;
use App\Models\Subject;
use Illuminate\Support\Facades\Log;

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
        try {
            //code...
            Leger::where('subject_id', $subject->id)->update(['subject_order' => $subject->order]);
        } catch (\Throwable $th) {
            //throw $th;
            Log::error('Leger tidak ditemukan untuk mata pelajaran tersebut.');
        }
    }
}
