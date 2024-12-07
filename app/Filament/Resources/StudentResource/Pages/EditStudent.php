<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use JoseEspinal\RecordNavigation\Traits\HasRecordNavigation;

class EditStudent extends EditRecord
{
    use HasRecordNavigation;

    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        $existingActions = [
            // Actions\DeleteAction::make(),
        ];

        return array_merge($existingActions, $this->getNavigationActions());
    }
}
