<?php

namespace App\Filament\Resources\QuranGradeResource\Pages;

use App\Filament\Resources\QuranGradeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQuranGrade extends EditRecord
{
    protected static string $resource = QuranGradeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
