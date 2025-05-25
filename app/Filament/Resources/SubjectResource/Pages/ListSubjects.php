<?php

namespace App\Filament\Resources\SubjectResource\Pages;

use App\Enums\CategoryLegerEnum;
use App\Filament\Resources\SubjectResource;
use App\Imports\SubjectImport;
use App\Jobs\UpdateTeacherSubjectCompetencyJob;
use App\Models\Competency;
use App\Models\TeacherSubject;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListSubjects extends ListRecords
{
    protected static string $resource = SubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('updateCompetency')
                ->label('Update Kompetensi Guru')
                ->color('success')
                ->icon('heroicon-o-arrow-path')
                ->requiresConfirmation()
                ->action(function () {
                    UpdateTeacherSubjectCompetencyJob::dispatch();

                    Notification::make()
                        ->title('Proses Update Kompetensi Dimulai')
                        ->body('Job untuk update kompetensi guru telah dijalankan di background')
                        ->success()
                        ->send();
                }),
            \EightyNine\ExcelImport\ExcelImportAction::make()
                ->color("primary")
                ->closeModalByClickingAway(false)
                ->slideOver()
                ->use(SubjectImport::class)
                ->sampleExcel(
                    sampleData: [
                        [
                            'nama_mata_pelajaran',
                            'kode_mata_pelajaran',
                            'urutan_mata_pelajaran',
                        ]
                    ],
                    fileName: 'subject-template.xlsx',
                    sampleButtonLabel: 'Download Template',
                ),

            Actions\Action::make('resetCompetency')
                ->label('Reset Kompetensi Guru')
                ->color('danger')
                ->icon('heroicon-o-arrow-path')
                ->requiresConfirmation()
                ->action(function () {
                    // Logic to reset competencies
                    $teacherSubjects = TeacherSubject::get();

                    // delete all competencies by teacher_subject_id
                    Competency::whereIn('teacher_subject_id', $teacherSubjects->pluck('id'))->delete();
                    // Loop through each TeacherSubject and create competencies
                    if ($teacherSubjects->isEmpty()) {
                        Notification::make()
                            ->title('Tidak Ada Kompetensi untuk Direset')
                            ->body('Tidak ada kompetensi yang ditemukan untuk direset.')
                            ->warning()
                            ->send();
                        return;
                    }

                    foreach ($teacherSubjects as $teacherSubject) {
                        // Update atau create kompetensi half_semester
                        Competency::create(
                            [
                                'teacher_subject_id' => $teacherSubject->id,
                                'code' => CategoryLegerEnum::HALF_SEMESTER->value,
                                'description' => CategoryLegerEnum::HALF_SEMESTER->getLabel(),
                                'passing_grade' => 70,
                                'half_semester' => true
                            ]
                        );

                        // Update atau create kompetensi full_semester
                        Competency::create([
                                'teacher_subject_id' => $teacherSubject->id,
                                'code' => CategoryLegerEnum::FULL_SEMESTER->value,
                                'description' => CategoryLegerEnum::FULL_SEMESTER->getLabel(),
                                'passing_grade' => 70,
                                'half_semester' => false
                            ]
                        );
                    }

                    // This should be implemented based on your application's requirements
                    Notification::make()
                        ->title('Kompetensi Guru Telah Direset')
                        ->body('Semua kompetensi guru telah direset.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
