<?php

namespace App\Filament\Resources\TeacherGradeResource\Widgets;

use App\Models\TeacherGrade;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TeacherGradeWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Jumlah Kelas', TeacherGrade::count()),
        ];
    }
}
