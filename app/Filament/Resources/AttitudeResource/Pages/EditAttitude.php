<?php

namespace App\Filament\Resources\AttitudeResource\Pages;

use App\Filament\Resources\AttitudeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAttitude extends EditRecord
{
    protected static string $resource = AttitudeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
