<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeacherResource\Pages;
use App\Filament\Resources\TeacherResource\RelationManagers;
use App\Models\Teacher;
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

class TeacherResource extends Resource
{
    protected static ?string $model = Teacher::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return __('teacher.list.teacher');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('teacher.list.name'))
                    ->required(),
                Select::make('gender')
                    ->label(__('teacher.list.gender'))
                    ->options([
                        'laki-laki' => 'laki-laki',
                        'perempuan' => 'perempuan'
                    ])
                    ->required(),
                TextInput::make('nip')
                    ->label(__('teacher.create.nip')),
                TextInput::make('nuptk')
                    ->label(__('teacher.create.nuptk')),
                FileUpload::make('signature')
                    ->label(__('teacher.create.signature'))
                    ->maxSize('1000')
                    ->image()
                    ->directory('teacher-signature')
                    ->downloadable()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('teacher.list.name'))
                    ->sortable(),
                TextColumn::make('gender')
                    ->label(__('teacher.list.gender'))
                    ->sortable(),
                ImageColumn::make('signature')
                    ->label(__('teacher.list.signature'))
                    ->size('50')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListTeachers::route('/'),
            'create' => Pages\CreateTeacher::route('/create'),
            'edit' => Pages\EditTeacher::route('/{record}/edit'),
        ];
    }
}
