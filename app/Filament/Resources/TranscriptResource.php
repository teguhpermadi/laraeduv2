<?php

namespace App\Filament\Resources;

use App\Enums\TranscriptEnum;
use App\Filament\Resources\TranscriptResource\Pages;
use App\Filament\Resources\TranscriptResource\RelationManagers;
use App\Models\Grade;
use App\Models\StudentGrade;
use App\Models\Subject;
use App\Models\Transcript;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TranscriptResource extends Resource
{
    protected static ?string $model = Transcript::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('academic_year_id')
                    ->default(session('academic_year_id')),
                Select::make('grade_id')
                    ->label('Grade')
                    ->live()
                    ->options(Grade::all()->pluck('name', 'id'))
                    ->required(),
                Select::make('subject_id')
                    ->label('Subject')
                    ->reactive()
                    ->options(
                        function (callable $get) {
                            return Subject::get()->pluck('name', 'id');
                        }
                    )
                    ->required(),
                Radio::make('type')
                    ->label('Type')
                    ->options(TranscriptEnum::class)
                    ->required(),
                Select::make('student_id')
                    ->label('Student')
                    ->reactive()
                    ->options(
                        function (callable $get) {
                            $studentGrade = StudentGrade::where('grade_id', $get('grade_id'))
                                ->where('academic_year_id', $get('academic_year_id'))
                                ->with('student')
                                ->get();
                            return $studentGrade->pluck('student.name', 'student.id');
                        }
                    )
                    ->required(),
                TextInput::make('score')
                        ->numeric()
                        ->inputMode('decimal')
                        ->minValue(1)
                        ->maxValue(100)
                        ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subject.name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('score')
                    ->sortable(),
                TextColumn::make('type')
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->groups([
                'student.name',
                'subject.name',
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
            'index' => Pages\ListTranscripts::route('/'),
            'create' => Pages\CreateTranscript::route('/create'),
            'edit' => Pages\EditTranscript::route('/{record}/edit'),
        ];
    }
}
