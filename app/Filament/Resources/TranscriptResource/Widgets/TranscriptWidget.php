<?php

namespace App\Filament\Resources\TranscriptResource\Widgets;

use App\Settings\TranscriptWeight;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TranscriptWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $transcriptWeightSetting = app(TranscriptWeight::class);
        $weight_report = $transcriptWeightSetting->weight_report;
        $weight_written_exam = $transcriptWeightSetting->weight_written_exam;
        $weight_practical_exam = $transcriptWeightSetting->weight_practical_exam;
        $description = 'Rapor '. $weight_report . '%, Tulis ' . $weight_written_exam . '%, Praktek ' . $weight_practical_exam . '%';
        return [
            Stat::make('Bobot Transkrip', $description),
        ];
    }
}
