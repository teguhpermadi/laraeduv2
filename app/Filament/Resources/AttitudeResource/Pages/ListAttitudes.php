<?php

namespace App\Filament\Resources\AttitudeResource\Pages;

use App\Filament\Resources\AttitudeResource;
use App\Models\Attitude;
use App\Models\TeacherGrade;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListAttitudes extends ListRecords
{
    protected static string $resource = AttitudeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
            Action::make('generateAttitude')
                ->label('Buat Penilaian Sikap')
                ->icon('heroicon-o-document-text')
                // ->color('success')
                ->action(function() {
                    // get teacher id
                    $teacherId = auth()->user()->userable->userable_id;
                    // get grade id
                    $gradeId = TeacherGrade::where('teacher_id', $teacherId)
                        ->where('academic_year_id', session('academic_year_id'))
                        ->first()
                        ->grade_id;

                    // dapatkan semua student berdasarkan teachergrade dan studentgrade
                    $students = TeacherGrade::where('teacher_id', $teacherId)
                        ->where('academic_year_id', session('academic_year_id'))
                        ->first()
                        ->studentGrade
                        ->pluck('student_id');

                    // dd($students);

                    // foreach student
                    foreach ($students as $student) {
                        // update or create attendance
                        Attitude::updateOrCreate(
                            [
                                'student_id' => $student,
                                'academic_year_id' => session('academic_year_id'),
                                'grade_id' => $gradeId
                            ],
                            [
                                'attitude_religius' => 3,
                                'attitude_social' => 3,
                            ]
                        );
                    }

                    // buatkan notifikasi bahasa indonesia
                    Notification::make()
                        ->title('Sikap Berhasil Dibuat')
                        ->body('Sikap berhasil dibuat untuk semua siswa')
                        ->send();
                }),
        ];
    }
}
