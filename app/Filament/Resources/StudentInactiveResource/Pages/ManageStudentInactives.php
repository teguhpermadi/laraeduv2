<?php

namespace App\Filament\Resources\StudentInactiveResource\Pages;

use App\Filament\Resources\StudentInactiveResource;
use App\Models\StudentInactive;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageStudentInactives extends ManageRecords
{
    protected static string $resource = StudentInactiveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->slideOver()
                ->using(function($data){
                    foreach ($data['student_ids'] as $student_id) {
                        $data = [
                            'academic_year_id' => $data['academic_year_id'],
                            'student_id' => $student_id,
                        ];

                        StudentInactive::create($data);
                    }
                }),
        ];
    }
}
