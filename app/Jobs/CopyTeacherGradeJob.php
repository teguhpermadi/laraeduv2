<?php

namespace App\Jobs;

use App\Models\TeacherGrade;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class CopyTeacherGradeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected string $sourceAcademicYearId,
        protected string $targetAcademicYearId
    ) {}

    public function handle(): void
    {
        $teacherGrades = TeacherGrade::withoutGlobalScopes()
            ->where('academic_year_id', $this->sourceAcademicYearId)
            ->get();

        $copiedCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;

        foreach ($teacherGrades as $teacherGrade) {
            try {
                $result = TeacherGrade::updateOrCreate(
                    [
                        'academic_year_id' => $this->targetAcademicYearId,
                        'teacher_id' => $teacherGrade->teacher_id,
                        'grade_id' => $teacherGrade->grade_id,
                    ],
                    [
                        'curriculum' => $teacherGrade->curriculum,
                    ]
                );

                if ($result->wasRecentlyCreated) {
                    $copiedCount++;
                } else {
                    $updatedCount++;
                }

                // log info
                Log::info('Data wali kelas berhasil disalin', [
                    'teacher_id' => $teacherGrade->teacher_id,
                    'grade_id' => $teacherGrade->grade_id,
                    'curriculum' => $teacherGrade->curriculum,
                    'academic_year_id' => $this->targetAcademicYearId,
                ]);

            } catch (Throwable $e) {
                Log::warning('Error saat menyalin data wali kelas', [
                    'error' => $e->getMessage(),
                    'teacher_id' => $teacherGrade->teacher_id,
                    'grade_id' => $teacherGrade->grade_id,
                ]);
                $skippedCount++;
            }
        }
    }
}