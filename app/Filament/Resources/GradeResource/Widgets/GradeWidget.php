<?php

namespace App\Filament\Resources\GradeResource\Widgets;

use App\Models\Grade;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GradeWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Jumlah Kelas', Grade::whereHas('studentGrade')->count()),
        ];
    }
}
