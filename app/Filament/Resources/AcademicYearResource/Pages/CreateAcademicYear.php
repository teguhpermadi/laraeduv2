<?php

namespace App\Filament\Resources\AcademicYearResource\Pages;

use App\Filament\Resources\AcademicYearResource;
use App\Models\LegerWeight;
use Filament\Resources\Pages\CreateRecord;

class CreateAcademicYear extends CreateRecord
{
    protected static string $resource = AcademicYearResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $data = $this->data;

        LegerWeight::create([
            'academic_year_id' => $this->record->id,
            'teacher_subject_id' => null,
            'daily_weight' => $data['daily_weight'] ?? 0,
            'mid_weight' => $data['mid_weight'] ?? 0,
            'final_weight' => $data['final_weight'] ?? 0,
        ]);
    }
}
