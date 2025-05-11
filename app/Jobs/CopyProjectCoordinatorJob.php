<?php

namespace App\Jobs;

use App\Models\ProjectCoordinator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class CopyProjectCoordinatorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected string $sourceAcademicYearId,
        protected string $targetAcademicYearId
    ) {}

    public function handle(): void
    {
        $projectCoordinators = ProjectCoordinator::withoutGlobalScopes()
            ->where('academic_year_id', $this->sourceAcademicYearId)
            ->get();

        $copiedCount = 0;
        $updatedCount = 0;
        $skippedCount = 0;
        
        foreach ($projectCoordinators as $projectCoordinator) {
            try {
                $result = ProjectCoordinator::updateOrCreate(
                    [
                        'academic_year_id' => $this->targetAcademicYearId,
                        'teacher_id' => $projectCoordinator->teacher_id,
                        'grade_id' => $projectCoordinator->grade_id,
                    ],
                    [
                        // No additional fields to update
                    ]
                );

                if ($result->wasRecentlyCreated) {
                    $copiedCount++;
                } else {
                    $updatedCount++;
                }

                Log::info('Data project coordinator berhasil disalin', [
                    'teacher_id' => $projectCoordinator->teacher_id,
                    'grade_id' => $projectCoordinator->grade_id,
                    'academic_year_id' => $this->targetAcademicYearId,
                ]);
            } catch (Throwable $e) {
                Log::warning('Error saat menyalin data project coordinator', [
                    'error' => $e->getMessage(),
                    'teacher_id' => $projectCoordinator->teacher_id,
                    'grade_id' => $projectCoordinator->grade_id,
                ]);
                $skippedCount++;
            }
        }
    }
}