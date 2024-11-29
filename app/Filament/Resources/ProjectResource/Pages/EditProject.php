<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Models\Project;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
            // action assesmen
            Actions\Action::make('assesment')
                ->color('success')
                ->url(fn (Project $record) => route('filament.admin.resources.projects.assesment', $record)),
        ];
    }
}
