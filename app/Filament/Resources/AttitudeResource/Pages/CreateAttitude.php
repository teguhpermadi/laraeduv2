<?php

namespace App\Filament\Resources\AttitudeResource\Pages;

use App\Filament\Resources\AttitudeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateAttitude extends CreateRecord
{
    protected static string $resource = AttitudeResource::class;

    // redirect to index page
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
