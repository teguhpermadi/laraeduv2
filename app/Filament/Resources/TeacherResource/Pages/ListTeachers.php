<?php

namespace App\Filament\Resources\TeacherResource\Pages;

use App\Filament\Resources\TeacherResource;
use App\Filament\Resources\TeacherResource\Widgets\TeacherWidget;
use App\Imports\TeacherImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTeachers extends ListRecords
{
    protected static string $resource = TeacherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            \EightyNine\ExcelImport\ExcelImportAction::make()
                ->color("primary")
                ->slideOver()
                ->use(TeacherImport::class)
                ->sampleExcel(
                    sampleData: [
                        [
                            "nama_lengkap",
                            "jenis_kelamin",
                            "username",
                            "password",
                        ]
                    ],
                    fileName: 'teacher-template.xlsx',
                    // exportClass: App\Exports\SampleExport::class, 
                    sampleButtonLabel: 'Download Template',
                    // customiseActionUsing: fn(Action $action) => $action->color('secondary')
                    //     ->icon('heroicon-m-clipboard')
                    //     ->requiresConfirmation(),
                ),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TeacherWidget::class,
        ];
    }
}
