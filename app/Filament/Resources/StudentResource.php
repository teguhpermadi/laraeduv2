<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Filament\Resources\StudentResource\RelationManagers\DataStudentRelationManager;
use App\Filament\Resources\StudentResource\RelationManagers\StudentGradeRelationManager;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Guava\FilamentModalRelationManagers\Actions\RelationManagerAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Pengaturan';

    public static function getModelLabel(): string
    {
        return __('student.student');
    }
    protected static ?int $navigationSort = 2;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nisn')
                    ->label(__('student.nisn'))
                    ->required(),
                TextInput::make('nis')
                    ->label(__('student.nis'))
                    ->required(),
                TextInput::make('name')
                    ->label(__('student.name'))
                    ->required(),
                TextInput::make('nick_name')
                    ->label(__('student.nick_name')),
                Select::make('gender')
                    ->label(__('student.gender'))
                    ->options([
                        'laki-laki' => 'laki-laki',
                        'perempuan' => 'perempuan'
                    ])
                    ->required(),
                TextInput::make('city_born')
                    ->label(__('student.city_born')),
                DatePicker::make('birthday')
                    ->label(__('student.birthday')),
                Toggle::make('active')
                    ->label(__('student.active')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('student.name'))
                    ->searchable(),
                TextColumn::make('gender')
                    ->label(__('student.gender')),
                TextColumn::make('nis')
                    ->label(__('student.nis')),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                RelationManagerAction::make('data-student-relation-manager')
                    ->label('data')
                    ->button()
                    ->slideOver()
                    ->relationManager(DataStudentRelationManager::class),
                RelationManagerAction::make('student-grade-relation-manager')
                    ->label('kelas')
                    ->button()
                    ->slideOver()
                    ->relationManager(StudentGradeRelationManager::class),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            StudentGradeRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
