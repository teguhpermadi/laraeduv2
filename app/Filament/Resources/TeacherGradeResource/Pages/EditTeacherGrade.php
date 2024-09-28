<?php

namespace App\Filament\Resources\TeacherGradeResource\Pages;

use App\Filament\Resources\TeacherGradeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTeacherGrade extends EditRecord
{
    protected static string $resource = TeacherGradeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
