<?php

namespace App\Jobs;

use App\Models\StudentGrade;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class CopyStudentGradeJob implements ShouldQueue
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
        $studentGrades = StudentGrade::withoutGlobalScopes()
            ->where('academic_year_id', $this->sourceAcademicYearId)
            ->get();

        $copiedCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;

        foreach ($studentGrades as $studentGrade) {
            try {
                // Cari berdasarkan unique constraint
                $result = StudentGrade::updateOrCreate(
                    [
                        'academic_year_id' => $this->targetAcademicYearId,
                        'student_id' => $studentGrade->student_id,
                        'grade_id' => $studentGrade->grade_id,
                    ],
                    [
                        // Tidak ada field yang perlu diupdate, hanya perlu memastikan record ada
                    ]
                );

                // Check if the record was created or updated
                if ($result->wasRecentlyCreated) {
                    $copiedCount++;
                } else {
                    $updatedCount++;
                }

                // log info
                Log::info('Data kelas siswa berhasil disalin', [
                    'student_id' => $studentGrade->student_id,
                    'grade_id' => $studentGrade->grade_id,
                    'academic_year_id' => $this->targetAcademicYearId,
                ]);

            } catch (Throwable $e) {
                // Log error dan lanjutkan ke record berikutnya
                Log::warning('Error saat menyalin data kelas siswa', [
                    'error' => $e->getMessage(),
                    'student_id' => $studentGrade->student_id,
                    'grade_id' => $studentGrade->grade_id,
                ]);
                $skippedCount++;
            }
        }
    }
}
