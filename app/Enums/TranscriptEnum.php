<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TranscriptEnum: string implements HasLabel
{
    case REPORT_SCORE = 'nilai_rapor';
    case WRITTEN_EXAM = 'ujian_tulis';
    case PRACTICAL_EXAM = 'ujian_praktek';

    public function getLabel(): string
    {
        return match ($this) {
            self::REPORT_SCORE => 'Nilai Rapor',
            self::WRITTEN_EXAM => 'Ujian Tulis',
            self::PRACTICAL_EXAM => 'Ujian Praktek',
        };
    }
}
