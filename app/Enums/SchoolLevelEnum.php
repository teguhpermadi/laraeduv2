<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum SchoolLevelEnum: string implements HasLabel
{
    case PAUD = 'paud';
    case SD = 'sd';
    case SMP = 'smp';
    case SMA = 'sma';

    public function getLabel(): string
    {
        return match ($this) {
            self::PAUD => 'Pendidikan Anak Usia Dini',
            self::SD => 'Sekolah Dasar sederajat',
            self::SMP => 'Sekolah Menengah Pertama sederajat',
            self::SMA => 'Sekolah Menengah Atas sederajat', 
        };
    }
}
