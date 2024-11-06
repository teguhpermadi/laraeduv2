<?php

namespace App\Filament\Resources\ProjectCoordinatorResource\Pages;

use App\Filament\Resources\ProjectCoordinatorResource;
use App\Models\Teacher;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Model;

class ManageProjectCoordinators extends ManageRecords
{
    protected static string $resource = ProjectCoordinatorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->using(function (array $data, string $model): Model {
                $user = Teacher::find($data['teacher_id'])->userable->user;
                $user->assignRole('project coordinator');
                return $model::create($data);
            }),
        ];
    }
}
