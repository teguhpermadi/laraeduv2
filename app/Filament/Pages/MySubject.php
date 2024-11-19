<?php

namespace App\Filament\Pages;

use App\Enums\CurriculumEnum;
use App\Models\TeacherSubject;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class MySubject extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Pelajaran Ku';

    protected static string $view = 'filament.pages.my-subject';

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
                ->searchable(),
            TextColumn::make('grade.name')
                ->label(__('my-subject.grade'))
                ->searchable(),
            TextColumn::make('teacherGrade.curriculum'),
            TextColumn::make('passing_grade')
                ->label(__('my-subject.passing_grade')),

        ])
        ->filters([
            // ...
        ])
        ->actions([
            Action::make('assesment')
                ->button()
                ->url(fn (TeacherSubject $record): string => route('filament.admin.pages.assessment.{id}', $record)),
            Action::make('leger')
                ->button()
                ->url(fn (TeacherSubject $record): string => route('filament.admin.pages.leger.{id}', $record)),
        ]);
    }
}
