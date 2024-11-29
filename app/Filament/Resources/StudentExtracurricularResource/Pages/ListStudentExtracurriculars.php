<?php

namespace App\Filament\Resources\StudentExtracurricularResource\Pages;

use App\Filament\Resources\StudentExtracurricularResource;
use App\Models\Extracurricular;
use App\Models\TeacherExtracurricular;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListStudentExtracurriculars extends ListRecords
{
    protected static string $resource = StudentExtracurricularResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    // buatkan tab berdasarkan jenis extracurricular
    public function getTabs(): array
    {
        $tabs = [];
        // cek siapa user yang login
        $user = auth()->user();
        if ($user->hasRole('super_admin')) {
            // tampilkan semua tab extracurricular
            $extracurricular = Extracurricular::all();
            foreach ($extracurricular as $extracurricular) {
                $tabs[] = Tab::make($extracurricular->name)
                    ->modifyQueryUsing(function (Builder $query) use ($extracurricular) {
                        $query->where('extracurricular_id', $extracurricular->id);
                    });
            }   
        } elseif ($user->hasRole('teacher_extracurricular')) {
            // tampilkan data berdasarkan teacher extracurricular yang dimiliki oleh user
            $extracurricular = TeacherExtracurricular::where('teacher_id', $user->userable->userable_id)->get();
            foreach ($extracurricular as $extracurricular) {
                $tabs[] = Tab::make($extracurricular->extracurricular->name)
                    ->modifyQueryUsing(function (Builder $query) use ($extracurricular) {
                        $query->where('extracurricular_id', $extracurricular->extracurricular_id);
                    });
            }
        }
        
        return $tabs;
    }
}
