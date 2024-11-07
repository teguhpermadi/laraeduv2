<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SchoolResource\Pages;
use App\Filament\Resources\SchoolResource\RelationManagers;
use App\Models\School;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Filament\Tables\Actions\Action;

class SchoolResource extends Resource
{
    protected static ?string $model = School::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?string $modelLabel = 'Sekolah';
    
    protected static ?string $pluralModelLabel = 'Sekolah';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('school.fields.name'))
                    ->required(),
                TextInput::make('address')
                    ->label(__('school.fields.address'))
                    ->required(),
                Select::make('village_id')
                    ->label(__('school.fields.village_id'))
                    ->options([]),
                TextInput::make('nsm')
                    ->label(__('school.fields.nsm'))
                    ->numeric(),
                TextInput::make('npsn')
                    ->label(__('school.fields.npsn'))
                    ->numeric(),
                TextInput::make('email')
                    ->label(__('school.fields.email'))
                    ->email(),
                TextInput::make('phone')
                    ->label(__('school.fields.phone'))
                    ->tel()
                    ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/'),
                TextInput::make('website')
                    ->label(__('school.fields.website')),
                TextInput::make('foundation')
                    ->label(__('school.fields.foundation')),
                FileUpload::make('logo')
                    ->label(__('school.fields.logo'))
                    ->directory('uploads')
                    ->image()
                    ->openable()
                    ->getUploadedFileNameForStorageUsing(
                        function (TemporaryUploadedFile $file) {
                            return 'logo-school.' . $file->getClientOriginalExtension();
                        }
                    )
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('logo'),
                TextColumn::make('name')
                    ->label(__('school.fields.name')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([])
            ->emptyStateActions([
                Action::make('create')
                    ->label(__('school.actions.create'))
                    ->url(route('filament.admin.resources.schools.create'))
                    ->icon('heroicon-m-plus')
                    ->button()
                    ->visible(fn () => School::count() === 0),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchools::route('/'),
            'create' => Pages\CreateSchool::route('/create'),
            'view' => Pages\ViewSchool::route('/{record}'),
            'edit' => Pages\EditSchool::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return School::count() === 0;
    }
}
