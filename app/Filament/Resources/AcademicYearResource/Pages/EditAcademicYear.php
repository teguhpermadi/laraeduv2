<?php

namespace App\Filament\Resources\AcademicYearResource\Pages;

use App\Filament\Resources\AcademicYearResource;
use App\Models\LegerWeight;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAcademicYear extends EditRecord
{
    protected static string $resource = AcademicYearResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $weight = LegerWeight::where('academic_year_id', $this->record->id)
            ->whereNull('teacher_subject_id')
            ->first();

        $data['daily_weight'] = $weight?->daily_weight ?? 0;
        $data['mid_weight'] = $weight?->mid_weight ?? 0;
        $data['final_weight'] = $weight?->final_weight ?? 0;

        return $data;
    }

    protected function afterSave(): void
    {
        $data = $this->data;

        LegerWeight::updateOrCreate(
            [
                'academic_year_id' => $this->record->id,
                'teacher_subject_id' => null,
            ],
            [
                'daily_weight' => $data['daily_weight'] ?? 0,
                'mid_weight' => $data['mid_weight'] ?? 0,
                'final_weight' => $data['final_weight'] ?? 0,
            ]
        );
    }
}
