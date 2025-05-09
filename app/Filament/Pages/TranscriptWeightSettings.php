<?php

namespace App\Filament\Pages;

use App\Settings\TranscriptWeight;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class TranscriptWeightSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = TranscriptWeight::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('dataset1')
                    ->columns(3)
                    ->schema([
                        TextInput::make('weight_report1')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100)
                            ->required(),
                        TextInput::make('weight_written_exam1')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100)
                            ->required(),
                        TextInput::make('weight_practical_exam1')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100)
                            ->required(),
                    ]),
                Fieldset::make('dataset2')
                    ->columns(3)
                    ->schema([
                        TextInput::make('weight_report2')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100)
                            ->required(),
                        TextInput::make('weight_written_exam2')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100)
                            ->required(),
                        TextInput::make('weight_practical_exam2')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100)
                            ->required(),
                    ]),
            ]);
    }
}
