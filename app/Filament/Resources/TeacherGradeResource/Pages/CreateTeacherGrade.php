<?php

namespace App\Filament\Resources\TeacherGradeResource\Pages;

use App\Filament\Resources\TeacherGradeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTeacherGrade extends CreateRecord
{
    protected static string $resource = TeacherGradeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
