<?php

namespace App\Filament\Resources\GradeResource\Pages;

use App\Filament\Resources\GradeResource;
use App\Imports\GradeImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGrades extends ListRecords
{
    protected static string $resource = GradeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            \EightyNine\ExcelImport\ExcelImportAction::make()
                ->color("primary")
                ->slideOver()
                ->use(GradeImport::class)
                ->sampleExcel(
                    sampleData: [
                        [
                            'nama_kelas',
                            'jenjang',
                            'fase',
                        ]
                    ],
                    fileName: 'grade-template.xlsx',
                    // exportClass: App\Exports\SampleExport::class, 
                    sampleButtonLabel: 'Download Template',
                    // customiseActionUsing: fn(Action $action) => $action->color('secondary')
                    //     ->icon('heroicon-m-clipboard')
                    //     ->requiresConfirmation(),
                ),
        ];
    }
}
