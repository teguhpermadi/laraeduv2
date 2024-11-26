<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CategoryInclusive: string implements HasLabel
{
    case ADHD = 'ADHD';
    case AUTISM = 'Autism';
    case SLOW_LEARNER = 'Slow Learner';

    public function getLabel(): string
    {
        return match ($this) {
            self::ADHD => 'ADHD',
            self::AUTISM => 'Autism',
            self::SLOW_LEARNER => 'Slow Learner',
        };
    }
}
