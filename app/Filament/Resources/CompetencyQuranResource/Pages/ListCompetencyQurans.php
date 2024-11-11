<?php

namespace App\Filament\Resources\CompetencyQuranResource\Pages;

use App\Filament\Resources\CompetencyQuranResource;
use App\Models\TeacherQuranGrade;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;


class ListCompetencyQurans extends ListRecords
{
    protected static string $resource = CompetencyQuranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    // buatkan tab berdasarkan quran grade yang dimiliki oleh teacher
    public function getTabs(): array
    {
        $quranGrades = TeacherQuranGrade::myQuranGrade()->with('quranGrade')->get();
        $tabs = [];
        foreach ($quranGrades as $quranGrade) {
            $tabs[$quranGrade->id] = Tab::make($quranGrade->quranGrade->name)
                ->modifyQueryUsing(function(Builder $query) use ($quranGrade){
                    $query->where('teacher_quran_grade_id', $quranGrade->id);
                });
        }

        return $tabs;
    }
}
