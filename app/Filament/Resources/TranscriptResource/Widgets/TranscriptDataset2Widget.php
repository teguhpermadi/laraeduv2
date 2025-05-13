<?php

namespace App\Filament\Resources\TranscriptResource\Widgets;

use App\Models\Transcript;
use App\Settings\TranscriptWeight;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Support\Colors\Color;

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

        // group by subject
        $subjects = $transcript->groupBy('subject_id');
        $subjects = $subjects->map(function ($subject) {
            return $subject->sortByDesc('averageDataset2')->first();
        });

        $stats = [];
        // tertinggi dataset 2
        $stats[] = Stat::make('Tertinggi Dataset 2', $topDataset2->averageDataset2)
            ->description($topDataset2->student->name . ' - ' . $topDataset2->subject->name)
            ->color(Color::Green)
            ->extraAttributes(['class' => 'col-span-2']);
        // terendah dataset 2
        $stats[] = Stat::make('Terendah Dataset 2', $bottomDataset2->averageDataset2)
            ->description($bottomDataset2->student->name . ' - ' . $topDataset2->subject->name)
            ->color(Color::Red)
            ->extraAttributes(['class' => 'col-span-2']);

        // stats subject
        foreach ($subjects as $subject) {
            $stats[] = Stat::make('Tertinggi di ' . $subject->subject->name, $subject->averageDataset2)
                ->description($subject->student->name)
                ->color(Color::Gray)
                ->extraAttributes(['class' => 'col-span-2']);
        }

        return $stats;
    }
}
