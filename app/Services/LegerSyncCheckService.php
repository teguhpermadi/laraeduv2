<?php

namespace App\Services;

use App\Models\LegerRecap;
use App\Models\StudentCompetency;
use App\Models\TeacherSubject;

class LegerSyncCheckService
{
    public function overallNeedsSync(TeacherSubject $teacherSubject): bool
    {
        $latestRecap = LegerRecap::where('teacher_subject_id', $teacherSubject->id)
            ->latest('updated_at')
            ->first();

        if (! $latestRecap) {
            return true;
        }

        $latestSC = StudentCompetency::where('teacher_subject_id', $teacherSubject->id)
            ->latest('updated_at')
            ->first();

        if ($latestSC && $latestSC->updated_at->gt($latestRecap->updated_at)) {
            return true;
        }

        return false;
    }
}
