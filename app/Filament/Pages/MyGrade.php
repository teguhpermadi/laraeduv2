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

    // navigation label
    public static function getNavigationLabel(): string
    {
        return 'Kelas Ku';
    }

    public static function getModelLabel(): string
    {
        return 'Kelas Ku';
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
                    ->button(),
                // action identitas raport  
                Action::make('identitas')
                    ->label('Identitas')
                    ->button(),
                // action tengah semester
                // buat warna biru untuk action tengah semester 
                Action::make('middle')
                    ->label('Tengah Semester')
                    ->color('success')
                    ->button(),
                // action akhir semester
                Action::make('end')
                    ->label('Akhir Semester')
                    ->button(),
                // action project
                Action::make('project')
                    ->label('Project')
                    ->button(),
            ])
            ->paginated(false)
            ->modifyQueryUsing(fn(Builder $query) => $query->myGrade());
    }
}
