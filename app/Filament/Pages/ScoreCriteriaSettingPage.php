<?php

namespace App\Filament\Pages;

use App\Settings\ScoreCriteriaSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ScoreCriteriaSettingPage extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = ScoreCriteriaSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('grade_a')
                    ->label('Grade A Threshold')
                    ->required(),
                Forms\Components\TextInput::make('grade_b')
                    ->label('Grade B Threshold')
                    ->required(),
                Forms\Components\TextInput::make('grade_c')
                    ->label('Grade C Threshold')
                    ->required(),
                Forms\Components\TextInput::make('grade_d')
                    ->label('Grade D Threshold')
                    ->required(),
            ]);
    }
}
