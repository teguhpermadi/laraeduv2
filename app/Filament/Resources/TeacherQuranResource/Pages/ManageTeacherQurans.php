<?php

namespace App\Filament\Resources\TeacherQuranResource\Pages;

use App\Filament\Resources\TeacherQuranResource;
use App\Models\TeacherQuran;
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
                ->slideOver()
                ->using(function ($data) {
                    foreach ($data['teacher_ids'] as $teacher_id) {
                        $data = [
                            'academic_year_id' => $data['academic_year_id'],
                            'teacher_id' => $teacher_id,
                        ];

                        TeacherQuran::updateOrCreate($data);
                    }
                }),
        ];
    }
}
