<?php

namespace App\Filament\Pages;

use App\Models\Student;
use App\Models\StudentGrade;
use App\Models\TeacherGrade;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MyGrade extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.my-grade';

    protected static ?string $navigationGroup = 'Kelas Ku';

    protected static ?int $navigationSort = 1;

    // navigation label
    public static function getNavigationLabel(): string
    {
        return 'Cetak Raport';
    }

    public static function getModelLabel(): string
    {
        return 'Cetak Raport';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(StudentGrade::query())
            ->columns([
                TextColumn::make('student.nisn')
                    ->label('NISN'),
                TextColumn::make('student.name')
                    ->label('Nama Siswa')
                    ->searchable(),
            ])
            ->actions([
                // action cover raport
                Action::make('cover')
                    ->label('Cover')
                    ->url(fn($record) => route('report-cover', $record->student_id))
                    ->button(),
                // action identitas raport  
                Action::make('identitas')
                    ->label('Identitas')
                    ->url(fn($record) => route('report-cover-student', $record->student_id))
                    ->button(),
                // action tengah semester
                // buat warna biru untuk action tengah semester 
                Action::make('middle')
                    ->label('Tengah Semester')
                    ->url(fn($record) => route('report-half-semester', $record->student_id))
                    ->color('success')
                    ->button(),
                // action akhir semester
                Action::make('full')
                    ->label('Akhir Semester')
                    ->url(fn($record) => route('report-full-semester', $record->student_id))
                    ->button(),
                // action project
                Action::make('project')
                    ->label('Project')
                    ->url(fn($record) => route('report-project', $record->student_id))
                    ->button(),
            ])
            ->paginated(false)
            ->modifyQueryUsing(fn(Builder $query) => $query->myGrade());
    }
}
