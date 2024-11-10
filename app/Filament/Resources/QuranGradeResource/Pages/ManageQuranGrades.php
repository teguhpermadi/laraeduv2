<?php

namespace App\Filament\Resources\QuranGradeResource\Pages;

use App\Filament\Resources\QuranGradeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageQuranGrades extends ManageRecords
{
    protected static string $resource = QuranGradeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
