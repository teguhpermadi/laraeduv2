<?php

namespace App\Filament\Resources\SubjectResource\Pages;

use App\Filament\Resources\SubjectResource;
use App\Imports\SubjectImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubjects extends ListRecords
{
    protected static string $resource = SubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
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
                    // exportClass: App\Exports\SampleExport::class, 
                    sampleButtonLabel: 'Download Template',
                    // customiseActionUsing: fn(Action $action) => $action->color('secondary')
                    //     ->icon('heroicon-m-clipboard')
                    //     ->requiresConfirmation(),
                ),
        ];
    }
}
