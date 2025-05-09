<?php

namespace App\Filament\Resources\TranscriptResource\Widgets;

use App\Models\Transcript;
use App\Settings\TranscriptWeight;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TranscriptDataset1Widget extends BaseWidget
{
    protected function getHeading(): ?string
    {
        return 'Dataset 1';
    }

    protected function getDescription(): ?string
    {
        $transcriptWeight = app(TranscriptWeight::class);
        $description1 = 'Rapor ' . $transcriptWeight->weight_report1 . '%, Tulis ' . $transcriptWeight->weight_written_exam1 . '%, Praktek ' . $transcriptWeight->weight_practical_exam1 . '%';

        return $description1;
    }
    protected function getStats(): array
    {
        $transcript = Transcript::get();
        $topDataset1 = $transcript->sortByDesc('averageDataset1')->first();
        $bottomDataset1 = $transcript->sortBy('averageDataset1')->first();

        return [
            Stat::make('Tertinggi Dataset 1', $topDataset1->averageDataset1)
                ->description($topDataset1->student->name),
            Stat::make('Terendah Dataset 1', $bottomDataset1->averageDataset1)
                ->description($bottomDataset1->student->name),
        ];
    }
}
