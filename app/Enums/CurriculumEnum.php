<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CurriculumEnum: string implements HasLabel
{
    case KURMER = 'kurmer';
    case K13 = 'k13';

    public function getLabel(): string
    {
        return match($this) {
            self::KURMER => 'Kurikulum Merdeka',
            self::K13 => 'Kurikulum 2013',
        };
    }
}
