<?php

namespace App\Filament\Resources\StudentResource\Widgets;

use App\Models\Student;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StudentWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Jumlah Siswa', Student::count()),
            Stat::make('Siswa Laki-laki', Student::where('gender', 'laki-laki')->count()),
            Stat::make('Siswa Perempuan', Student::where('gender', 'perempuan')->count()),
        ];
    }
}
