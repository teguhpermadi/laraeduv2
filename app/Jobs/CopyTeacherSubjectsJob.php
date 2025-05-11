<?php

namespace App\Jobs;

use App\Models\TeacherSubject;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class CopyTeacherSubjectsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected string $sourceAcademicYearId,
        protected string $targetAcademicYearId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $teacherSubjects = TeacherSubject::withoutGlobalScopes()
            ->where('academic_year_id', $this->sourceAcademicYearId)
            ->get();

        $copiedCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;
        
        foreach ($teacherSubjects as $teacherSubject) {
            try {
                // Cari berdasarkan unique constraint
                $result = TeacherSubject::updateOrCreate(
                    [
                        'academic_year_id' => $this->targetAcademicYearId,
                        'grade_id' => $teacherSubject->grade_id,
                        'subject_id' => $teacherSubject->subject_id,
                    ],
                    [
                        'teacher_id' => $teacherSubject->teacher_id,
                        'time_allocation' => $teacherSubject->time_allocation,
                        'passing_grade' => $teacherSubject->passing_grade,
                    ]
                );

                // Check if the record was created or updated
                if ($result->wasRecentlyCreated) {
                    $copiedCount++;
                } else {
                    $updatedCount++;
                }

                // log info
                Log::info('Data guru dan mata pelajaran berhasil disalin', [
                    'teacher_id' => $teacherSubject->teacher_id,
                    'subject_id' => $teacherSubject->subject_id,
                    'grade_id' => $teacherSubject->grade_id,
                    'academic_year_id' => $this->targetAcademicYearId,
                ]);
            } catch (Throwable $e) {
                // Log error dan lanjutkan ke record berikutnya
                Log::warning('Error saat menyalin data guru dan mata pelajaran', [
                    'error' => $e->getMessage(),
                    'teacher_id' => $teacherSubject->teacher_id,
                    'subject_id' => $teacherSubject->subject_id,
                    'grade_id' => $teacherSubject->grade_id,
                ]);
                $skippedCount++;
            }
        }
    }
}
