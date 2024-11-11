<?php

namespace App\Filament\Resources\CompetencyQuranResource\Pages;

use App\Filament\Resources\CompetencyQuranResource;
use App\Models\TeacherQuranGrade;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompetencyQuran extends EditRecord
{
    protected static string $resource = CompetencyQuranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // mutate data sebelum di filling
    public function mutateFormDataBeforeFill(array $data): array
    {
        $teacherQuranGrade = TeacherQuranGrade::find($data['teacher_quran_grade_id']);
        $data['quran_grade_id'] = $teacherQuranGrade->quran_grade_id;
        return $data;
    }
}
