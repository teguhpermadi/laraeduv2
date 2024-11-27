<?php

namespace App\Filament\Pages;

use App\Enums\SchoolLevelEnum;
use App\Settings\SchoolSettings;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class SchoolSettingPage extends SettingsPage
{
    use HasPageShield;
    
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = SchoolSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('school_level')
                    ->label('Jenjang Sekolah')
                    ->options(SchoolLevelEnum::class)
                    ->required(),
                TextInput::make('school_name')
                    ->label('Nama Sekolah')
                    ->required(),
                Textarea::make('school_address')
                    ->label('Alamat')
                    ->required(),
                TextInput::make('school_nsm')
                    ->label('NSM')
                    ->numeric()
                    ->required(),
                TextInput::make('school_npsn')
                    ->label('NPSN')
                    ->numeric()
                    ->required(),
                TextInput::make('school_website')
                    ->label('Website')
                    ->required(),
                TextInput::make('school_phone')
                    ->label('Telpon')
                    ->required(),
                TextInput::make('school_email')
                    ->label('Email')
                    ->email()
                    ->required(),
                FileUpload::make('school_logo')
                    ->label(__('Logo'))
                    ->directory('uploads')
                    ->image()
                    ->openable()
                    ->getUploadedFileNameForStorageUsing(
                        function (TemporaryUploadedFile $file) {
                            return 'logo-school.' . $file->getClientOriginalExtension();
                        }
                    ),
            ]);
    }
}
