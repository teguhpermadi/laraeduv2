<?php

namespace App\Filament\Resources\AttendanceResource\Pages;

use App\Filament\Resources\AttendanceResource;
use App\Models\Attendance;
use App\Models\TeacherGrade;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
            Action::make('generateAttendance')
                ->label('Buat Kehadiran')
                ->icon('heroicon-o-document-text')
                // ->color('success')
                ->action(function() {
                    $grades = TeacherGrade::myGrade()->get();
                    
                    // foreach student
                    foreach ($grades as $grade) {
                        foreach ($grade->studentGrade as $student) {
                            Attendance::updateOrCreate(
                                [
                                    'student_id' => $student->student_id,
                                    'grade_id' => $grade->grade_id,
                                    'academic_year_id' => session('academic_year_id'),
                                ],
                                ['date' => now()]
                            );
                        }
                    }

                    // buatkan notifikasi bahasa indonesia
                    Notification::make()
                        ->title('Presensi Berhasil Dibuat')
                        ->body('Presensi berhasil dibuat untuk semua siswa')
                        ->send();
                }),
        ];
    }
}
