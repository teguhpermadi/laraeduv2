<?php

namespace App\Jobs;

use App\Enums\CategoryLegerEnum;
use App\Models\Competency;
use App\Models\TeacherSubject;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateTeacherSubjectCompetencyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        TeacherSubject::chunk(200, function ($teacherSubjects) {
            foreach ($teacherSubjects as $teacherSubject) {
                // Update atau create kompetensi half_semester
                Competency::updateOrCreate(
                    [
                        'teacher_subject_id' => $teacherSubject->id,
                        'code' => CategoryLegerEnum::HALF_SEMESTER->value
                    ],
                    [
                        'description' => CategoryLegerEnum::HALF_SEMESTER->getLabel(),
                        'passing_grade' => 70,
                        'half_semester' => true
                    ]
                );

                // Update atau create kompetensi full_semester
                Competency::updateOrCreate(
                    [
                        'teacher_subject_id' => $teacherSubject->id,
                        'code' => CategoryLegerEnum::FULL_SEMESTER->value
                    ],
                    [
                        'description' => CategoryLegerEnum::FULL_SEMESTER->getLabel(),
                        'passing_grade' => 70,
                        'half_semester' => false
                    ]
                );
            }
        });
    }
}