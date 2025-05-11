<?php

namespace App\Jobs;

use App\Models\StudentExtracurricular;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class CopyStudentExtracurricularJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected string $sourceAcademicYearId,
        protected string $targetAcademicYearId
    ) {}

    public function handle(): void
    {
        $studentExtracurriculars = StudentExtracurricular::withoutGlobalScopes()
            ->where('academic_year_id', $this->sourceAcademicYearId)
            ->get();

        $copiedCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;
        
        foreach ($studentExtracurriculars as $studentExtracurricular) {
            try {
                $result = StudentExtracurricular::updateOrCreate(
                    [
                        'academic_year_id' => $this->targetAcademicYearId,
                        'student_id' => $studentExtracurricular->student_id,
                        'extracurricular_id' => $studentExtracurricular->extracurricular_id,
                    ],
                    [
                        'score' => $studentExtracurricular->score,
                    ]
                );

                if ($result->wasRecentlyCreated) {
                    $copiedCount++;
                } else {
                    $updatedCount++;
                }

                Log::info('Data siswa ekstrakurikuler berhasil disalin', [
                    'student_id' => $studentExtracurricular->student_id,
                    'extracurricular_id' => $studentExtracurricular->extracurricular_id,
                    'academic_year_id' => $this->targetAcademicYearId,
                ]);
            } catch (Throwable $e) {
                Log::warning('Error saat menyalin data siswa ekstrakurikuler', [
                    'error' => $e->getMessage(),
                    'student_id' => $studentExtracurricular->student_id,
                    'extracurricular_id' => $studentExtracurricular->extracurricular_id,
                ]);
                $skippedCount++;
            }
        }
    }
}