<?php

namespace App\Filament\Resources\TranscriptResource\Widgets;

use App\Models\Transcript;
use App\Settings\TranscriptWeight;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TranscriptDataset2Widget extends BaseWidget
{
    protected function getHeading(): ?string
    {
        return 'Dataset 2';
    }

    protected function getDescription(): ?string
    {
        $transcriptWeight = app(TranscriptWeight::class);
        $description2 = 'Rapor ' . $transcriptWeight->weight_report2 . '%, Tulis ' . $transcriptWeight->weight_written_exam2 . '%, Praktek ' . $transcriptWeight->weight_practical_exam2 . '%';

        return $description2;
    }
    protected function getStats(): array
    {
        $transcript = Transcript::get();
        $topDataset2 = $transcript->sortByDesc('averageDataset2')->first();
        $bottomDataset2 = $transcript->sortBy('averageDataset2')->first();

        return [
            Stat::make('Tertinggi Dataset 2', $topDataset2->averageDataset2)
                ->description($topDataset2->student->name .  ' - ' .$topDataset2->subject->name),
            Stat::make('Terendah Dataset 2', $bottomDataset2->averageDataset2)
                ->description($bottomDataset2->student->name .  ' - ' . $topDataset2->subject->name),
        ];
    }
}
