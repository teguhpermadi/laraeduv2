<?php

namespace App\Jobs;

use App\Enums\CategoryLegerEnum;
use App\Models\Competency;
use App\Models\TeacherSubject;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\JobCompletedNotification;

class UpdateTeacherSubjectCompetencyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $academicYearId;

    public function __construct($academicYearId = null)
    {
        $this->academicYearId = $academicYearId;
    }

    public function handle(): void
    {
        try {
            Log::info('Memulai proses update kompetensi TeacherSubject pada academic year ID: ' . $this->academicYearId);

            $totalProcessed = 0;

            $query = TeacherSubject::withoutGlobalScopes()->query();

            $query->where('academic_year_id', $this->academicYearId);

            $teacherSubjects = $query->get();

            Log::info('Jumlah TeacherSubject yang ditemukan: ' . $teacherSubjects->count());

            // $query->chunk(200, function ($teacherSubjects) use ($totalProcessed) {
            // });
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
                $totalProcessed++;
            }
            
            Log::info('Proses update kompetensi selesai. Total diproses: ' . $totalProcessed);
        } catch (\Exception $e) {
            Log::error('Gagal menjalankan UpdateTeacherSubjectCompetencyJob', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
