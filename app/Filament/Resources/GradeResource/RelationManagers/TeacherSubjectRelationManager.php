<?php

namespace App\Filament\Resources\GradeResource\RelationManagers;

use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherSubject;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TeacherSubjectRelationManager extends RelationManager
{
    protected static string $relationship = 'teacherSubject';

    protected static bool $shouldSkipAuthorization = true;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('academic_year_id')
                    ->default(session()->get('academic_year_id')),
                Select::make('subject_id')
                    ->label('Mata Pelajaran')
                    ->options(Subject::all()->pluck('name', 'id'))
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
            ->recordTitleAttribute('subject_id')
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
                    ->label('Tambah Mata Pelajaran')
                    ->slideOver()
                    ->closeModalByClickingAway(false)
                    ->form([
                        Hidden::make('academic_year_id')
                            ->default(session()->get('academic_year_id')),
                        Repeater::make('items')
                            ->label('')
                            ->schema([
                                Select::make('subject_id')
                                    ->label('Mata Pelajaran')
                                    ->options(Subject::all()->pluck('name', 'id'))
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
                            ])
                            ->columns(2)
                            ->defaultItems(1)
                            ->minItems(1)
                            ->addActionLabel('Tambah Baris'),
                    ])
                    ->using(function (array $data): Model {
                        $gradeId = $this->getOwnerRecord()->getKey();
                        $academicYearId = $data['academic_year_id'] ?? session('academic_year_id');
                        $created = 0;
                        $skipped = 0;
                        $first = null;

                        foreach ($data['items'] as $item) {
                            $exists = TeacherSubject::where([
                                'academic_year_id' => $academicYearId,
                                'grade_id' => $gradeId,
                                'subject_id' => $item['subject_id'],
                                'teacher_id' => $item['teacher_id'],
                            ])->exists();

                            if ($exists) {
                                $skipped++;

                                continue;
                            }

                            $record = TeacherSubject::create([
                                'academic_year_id' => $academicYearId,
                                'grade_id' => $gradeId,
                                'subject_id' => $item['subject_id'],
                                'teacher_id' => $item['teacher_id'],
                                'time_allocation' => $item['time_allocation'] ?? 0,
                                'passing_grade' => $item['passing_grade'] ?? 70,
                            ]);

                            $first ??= $record;
                            $created++;
                        }

                        if ($created && ! $skipped) {
                            Notification::make()
                                ->title("{$created} data berhasil disimpan")
                                ->success()
                                ->send();
                        } elseif ($created && $skipped) {
                            Notification::make()
                                ->title("{$created} disimpan, {$skipped} dilewati (sudah ada)")
                                ->warning()
                                ->send();
                        } elseif ($skipped) {
                            Notification::make()
                                ->title('Semua data sudah ada, tidak ada yang disimpan')
                                ->danger()
                                ->send();
                        }

                        return $first ?? $data['items'][0];
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
}
