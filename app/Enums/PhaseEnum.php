<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PhaseEnum: string implements HasLabel
{
    case phaseFONDATION = 'fase pondasi';
    case phaseA = 'fase A';
    case phaseB = 'fase B';
    case phaseC = 'fase C';
    case phaseD = 'fase D';
    case phaseE = 'fase E';
    case phaseF = 'fase F';
    
    public function getLabel(): ?string
    {
        // return $this->name;
        return match ($this) {
            self::phaseFONDATION => 'Fase Pondasi',
            self::phaseA => 'Fase A',
            self::phaseB => 'Fase B',
            self::phaseC => 'Fase C',
            self::phaseD => 'Fase D',
            self::phaseE => 'Fase E',
            self::phaseF => 'Fase F',
        };
    }

    public static function getRandomValue(): string
    {
        return self::cases()[array_rand(self::cases())]->value;
    }
}
