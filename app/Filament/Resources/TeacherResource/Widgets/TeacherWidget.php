<?php

namespace App\Filament\Resources\TeacherResource\Widgets;

use App\Models\Teacher;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TeacherWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Jumlah ' . __('teacher.list.teacher'), Teacher::count()),
            Stat::make('Guru Laki-laki', Teacher::where('gender', 'laki-laki')->count()),
            Stat::make('Guru Perempuan', Teacher::where('gender', 'perempuan')->count()),
        ];
    }
}
