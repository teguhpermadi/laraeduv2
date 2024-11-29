<?php

namespace App\Filament\Resources\TeacherQuranResource\Pages;

use App\Filament\Resources\TeacherQuranResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTeacherQurans extends ManageRecords
{
    protected static string $resource = TeacherQuranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->closeModalByClickingAway(false)
                ->slideOver(),
        ];
    }
}
