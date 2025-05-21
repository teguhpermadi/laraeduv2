<?php

namespace App\Filament\Resources\SubjectResource\Pages;

use App\Filament\Resources\SubjectResource;
use App\Imports\SubjectImport;
use App\Jobs\UpdateTeacherSubjectCompetencyJob;
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
        ];
    }
}
