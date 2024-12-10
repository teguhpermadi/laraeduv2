<?php

namespace App\Filament\Pages;

use App\Models\TeacherQuranGrade;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class MyQuranGrade extends Page implements HasTable
{
    use HasPageShield;

    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.my-quran-grade';

    protected static ?string $navigationGroup = 'Mengaji';

    protected static ?string $modelLabel = 'Kelas Mengaji Ku';

    protected static ?string $pluralModelLabel = 'Kelas Mengaji Ku';

    protected static ?int $navigationSort = 2;


    public function table(Table $table): Table
    {
        return $table
            ->query(TeacherQuranGrade::query())
            ->columns([
                TextColumn::make('quranGrade.name')
                    ->label(__('quran-grade.fields.name.label')),
                TextColumn::make('student_quran_grade_count') 
                    ->label(__('quran-grade.fields.students.label'))
                    ->counts('studentQuranGrade')
                    ->suffix(' siswa'),
            ])
            ->actions([
                Action::make('assessment-quran')
                    ->label('Nilai')
                    ->url(fn (TeacherQuranGrade $record): string => route('filament.admin.pages.assessment-quran.{id}', $record->id))
                    ->button(),
            ])
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->myQuranGrade());
    }
}
