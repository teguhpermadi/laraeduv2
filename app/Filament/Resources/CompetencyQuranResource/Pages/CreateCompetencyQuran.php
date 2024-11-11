<?php

namespace App\Filament\Resources\CompetencyQuranResource\Pages;

use App\Filament\Resources\CompetencyQuranResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCompetencyQuran extends CreateRecord
{
    protected static string $resource = CompetencyQuranResource::class;

    // redirect ke index competency quran
    public function getRedirectUrl(): string
    {
        return CompetencyQuranResource::getUrl('index');
    }
}
