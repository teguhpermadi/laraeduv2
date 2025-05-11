<?php

namespace App\Jobs;

use App\Models\TeacherExtracurricular;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class CopyTeacherExtracurricularJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected string $sourceAcademicYearId,
        protected string $targetAcademicYearId
    ) {}

    public function handle(): void
    {
        $teacherExtracurriculars = TeacherExtracurricular::withoutGlobalScopes()
            ->where('academic_year_id', $this->sourceAcademicYearId)
            ->get();

        $copiedCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;
        
        foreach ($teacherExtracurriculars as $teacherExtracurricular) {
            try {
                $result = TeacherExtracurricular::updateOrCreate(
                    [
                        'academic_year_id' => $this->targetAcademicYearId,
                        'teacher_id' => $teacherExtracurricular->teacher_id,
                        'extracurricular_id' => $teacherExtracurricular->extracurricular_id,
                    ],
                    [
                        // Tidak ada field yang perlu diupdate selain yang ada di unique constraint
                    ]
                );

                if ($result->wasRecentlyCreated) {
                    $copiedCount++;
                } else {
                    $updatedCount++;
                }

                Log::info('Data guru ekstrakurikuler berhasil disalin', [
                    'teacher_id' => $teacherExtracurricular->teacher_id,
                    'extracurricular_id' => $teacherExtracurricular->extracurricular_id,
                    'academic_year_id' => $this->targetAcademicYearId,
                ]);
            } catch (Throwable $e) {
                Log::warning('Error saat menyalin data guru ekstrakurikuler', [
                    'error' => $e->getMessage(),
                    'teacher_id' => $teacherExtracurricular->teacher_id,
                    'extracurricular_id' => $teacherExtracurricular->extracurricular_id,
                ]);
                $skippedCount++;
            }
        }
    }
}