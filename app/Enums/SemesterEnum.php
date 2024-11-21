<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum SemesterEnum: string implements HasLabel
{
    case GANJIL = 'ganjil';
    case GENAP = 'genap';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::GANJIL => 'Ganjil',
            self::GENAP => 'Genap',
        };
    }
}
