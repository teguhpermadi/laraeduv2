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
                        Attendance::updateOrCreate(
                            [
                                'student_id' => $student,
                                'academic_year_id' => session('academic_year_id'),
                                'grade_id' => $gradeId
                            ],
                            ['date' => now()]
                        );
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
