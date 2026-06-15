<?php

namespace App\Filament\Resources\GradeResource\Pages;

use App\Filament\Resources\GradeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGrade extends EditRecord
{
    protected static string $resource = GradeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('teacherSubjects')
                ->label('Atur Mata Pelajaran')
                ->url(fn () => GradeResource::getUrl('teacher-subjects', ['record' => $this->record])),
            Actions\DeleteAction::make(),
        ];
    }
}
