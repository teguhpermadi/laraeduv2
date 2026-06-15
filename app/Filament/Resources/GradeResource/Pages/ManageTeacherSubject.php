<?php

namespace App\Filament\Resources\GradeResource\Pages;

use App\Filament\Resources\GradeResource;
use App\Models\Subject;
use App\Models\Teacher;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;

class ManageTeacherSubject extends ManageRelatedRecords
{
    protected static string $resource = GradeResource::class;

    protected static string $relationship = 'teacherSubject';

    protected static bool $shouldSkipAuthorization = true;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return __('subject.subject');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('academic_year_id')
                    ->default(session()->get('academic_year_id')),
                Select::make('subject_id')
                    ->label('Mata Pelajaran')
                    ->options(Subject::all()->pluck('name', 'id'))
                    ->unique(modifyRuleUsing: function (Unique $rule, callable $get) {
                        return $rule
                            ->where('academic_year_id', $get('academic_year_id'))
                            ->where('grade_id', $this->getOwnerRecord()->getKey())
                            ->where('subject_id', $get('subject_id'));
                    }, ignoreRecord: true)
                    ->required()
                    ->searchable(),
                Select::make('teacher_id')
                    ->label('Guru')
                    ->options(Teacher::all()->pluck('name', 'id'))
                    ->required()
                    ->searchable(),
                TextInput::make('time_allocation')
                    ->label('Alokasi Waktu')
                    ->numeric()
                    ->default(0),
                TextInput::make('passing_grade')
                    ->label('KKM')
                    ->numeric()
                    ->default(70),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subject.name')
                    ->label('Mata Pelajaran')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('teacher.name')
                    ->label('Guru')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('time_allocation')
                    ->label('Alokasi Waktu'),
                Tables\Columns\TextColumn::make('passing_grade')
                    ->label('KKM'),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->slideOver()
                    ->closeModalByClickingAway(false)
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['grade_id'] = $this->getOwnerRecord()->getKey();

                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver()
                    ->closeModalByClickingAway(false),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
