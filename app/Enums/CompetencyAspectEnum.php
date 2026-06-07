<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CompetencyAspectEnum: string implements HasLabel
{
    case KNOWLEDGE = 'knowledge';
    case SKILL = 'skill';

    public function getLabel(): string
    {
        return match($this) {
            self::KNOWLEDGE => 'Pengetahuan',
            self::SKILL => 'Keterampilan',
        };
    }
}
