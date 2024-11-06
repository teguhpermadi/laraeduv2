<?php

namespace App\Filament\Resources\AttitudeResource\Pages;

use App\Filament\Resources\AttitudeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttitudes extends ListRecords
{
    protected static string $resource = AttitudeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
