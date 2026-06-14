<?php

namespace App\Filament\Pages;

use App\Settings\TeacherWeightSetting;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class TeacherWeightSettingPage extends SettingsPage
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Settings';

    protected static string $settings = TeacherWeightSetting::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Toggle::make('can_edit_weight')
                    ->label('Izinkan guru mengubah bobot penilaian')
                    ->helperText('Jika diaktifkan, guru dapat mengubah bobot penilaian (harian, STS, SAS) pada halaman My Subject. Jika dinonaktifkan, hanya Super Admin yang dapat mengubahnya.'),
            ]);
    }
}
