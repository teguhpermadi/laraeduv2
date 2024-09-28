<?php

namespace App;

use Filament\Support\Contracts\HasLabel;

enum PhaseEnum: string implements HasLabel
{
    case phaseA = 'fase A';
    case phaseB = 'fase B';
    case phaseC = 'fase C';
    case phaseD = 'fase D';
    case phaseE = 'fase E';
    case phaseF = 'fase F';
    
    public function getLabel(): ?string
    {
        return $this->name;        
    }
}
