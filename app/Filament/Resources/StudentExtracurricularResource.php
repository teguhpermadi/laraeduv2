<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentExtracurricularResource\Pages;
use App\Filament\Resources\StudentExtracurricularResource\RelationManagers;
use App\Enums\LinkertScaleEnum;
use App\Models\Extracurricular;
use App\Models\Student;
use App\Models\StudentExtracurricular;
use App\Models\TeacherExtracurricular;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentExtracurricularResource extends Resource
{
    protected static ?string $model = StudentExtracurricular::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Pelajaran Ku';

    protected static ?string $modelLabel = 'Penilaian Ekstrakurikuler';
    
    protected static ?string $pluralModelLabel = 'Penilaian Ekstrakurikuler';

    protected static ?int $navigationSort = 9;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('academic_year_id')
                    ->default(session('academic_year_id')),
                Select::make('extracurricular_id')
                    ->label(__('extracurricular.extracurricular'))
                    ->options(Extracurricular::all()->pluck('name', 'id'))
                    ->live()
                    ->reactive(),
                Select::make('student_id')
                    ->label(__('extracurricular.student'))
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->options(function (Get $get) {
                        // tampilkan semua student yang belum memiliki student_extracurricular
                        $students = Student::query()
                            ->whereDoesntHave('studentExtracurricular', function ($query) use ($get) {
                                $query->where('extracurricular_id', $get('extracurricular_id'));
                            })
                            ->pluck('name', 'id');

                        return $students;
                    }
                ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('student.photo')
                    ->circular()
                    ->size(100),
                TextColumn::make('student.name')
                    ->label(__('extracurricular.student')),
                TextColumn::make('extracurricular.name')
                    ->label(__('extracurricular.extracurricular')),
                SelectColumn::make('score')
                    ->label(__('extracurricular.score'))
                    ->options(LinkertScaleEnum::class),
            ])
            ->filters([
                
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Action::make('Lihat foto')
                    ->slideOver()
                    ->modalWidth('sm')
                    ->modalHeading(fn (StudentExtracurricular $record) => $record->student->name)
                    ->modalContent(function (StudentExtracurricular $record) {
                        return view('student-extracurricular-preview', compact('record'));
                    })
            ], position: ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->recordUrl(null);
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
            'index' => Pages\ListStudentExtracurriculars::route('/'),
            'create' => Pages\CreateStudentExtracurricular::route('/create'),
            'edit' => Pages\EditStudentExtracurricular::route('/{record}/edit'),
        ];
    }
}
