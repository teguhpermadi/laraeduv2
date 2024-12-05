<?php

namespace App\Filament\Pages;

use App\Enums\CurriculumEnum;
use App\Models\TeacherSubject;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;

class MySubject extends Page implements HasTable
{
    use HasPageShield;
    
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Pelajaran Ku';

    protected static string $view = 'filament.pages.my-subject';

    protected static ?int $navigationSort = 8;


    public static function getNavigationLabel(): string
    {
        return __('my-subject.my-subject');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(TeacherSubject::query()->mySubject())
            ->columns([
                TextColumn::make('subject.name')
                    ->label(__('my-subject.subject'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('grade.name')
                    ->label(__('my-subject.grade'))
                    ->sortable()
                    ->searchable(),
                IconColumn::make('grade.is_inclusive')
                    ->label(__('grade.is_inclusive'))
                    ->boolean(),
                TextColumn::make('teacherGrade.curriculum')
                    ->label(__('my-subject.curriculum'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('passing_grade')
                    ->label(__('my-subject.passing_grade'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('legerRecapHalfSemester.updated_at')
                    ->wrapHeader()
                    ->label(__('my-subject.half_semester_last_update'))
                    ->dateTime('d M Y H:i'),
                TextColumn::make('legerRecapFullSemester.updated_at')
                    ->wrapHeader()
                    ->label(__('my-subject.full_semester_last_update'))
                    ->dateTime('d M Y H:i'),

            ])
            ->filters([
                // ...
            ])
            ->actions(
                [
                    Action::make('assesment')
                        ->button()
                        ->url(fn(TeacherSubject $record): string => route('filament.admin.pages.assessment.{id}', $record)),
                    Action::make('leger')
                        ->button()
                        ->url(fn(TeacherSubject $record): string => route('filament.admin.pages.leger.{id}', $record)),
                ],
                position: ActionsPosition::BeforeColumns
            );
    }
}
