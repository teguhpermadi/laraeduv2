<?php

namespace App\Filament\Resources\StudentInclusiveResource\Pages;

use App\Filament\Resources\StudentInclusiveResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageStudentInclusives extends ManageRecords
{
    protected static string $resource = StudentInclusiveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
