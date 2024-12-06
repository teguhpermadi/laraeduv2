<?php

namespace App\Filament\Pages;

use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\StudentGrade;
use App\Models\TeacherGrade;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\ActionSize;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MyGrade extends Page implements HasTable
{
    use HasPageShield;

    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.my-grade';

    protected static ?string $navigationGroup = 'Kelas Ku';

    protected static ?int $navigationSort = 10;

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
                Stack::make([
                    TextColumn::make('student.nisn')
                        ->label('NISN')
                        ->sortable(),
                    TextColumn::make('student.name')
                        ->label('Nama Siswa')
                        ->wrap()
                        ->searchable()
                        ->sortable(),
                    TextColumn::make('grade.name')
                        ->label('Kelas')
                        ->sortable(),
                ]),
                IconColumn::make('student.attendanceFirst.status')
                    ->label('Naik Kelas')
                    ->boolean()
                    ->hidden(
                        function () {
                            // sembunyikan jika academic semester genap
                            $academic = AcademicYear::find(session('academic_year_id'));

                            if ($academic->semester == 'ganjil') {
                                return true;
                            }

                            return false;
                        }
                    ),
            ])
            ->actions([
                // action cover raport
                Action::make('cover')
                    ->label('Cover')
                    ->size(ActionSize::Small)
                    ->color(Color::Emerald)
                    ->url(fn($record) => route('report-cover', $record->student_id))
                    ->button(),
                // action identitas raport  
                Action::make('identitas')
                    ->size(ActionSize::Small)
                    ->label('Identitas')
                    ->color(Color::Fuchsia)
                    ->url(fn($record) => route('report-cover-student', $record->student_id))
                    ->button(),
                // action tengah semester
                // buat warna biru untuk action tengah semester 
                Action::make('middle')
                    ->label('Tengah Semester')
                    ->size(ActionSize::Small)
                    ->color(Color::Amber)
                    ->url(fn($record) => route('report-half-semester', $record->student_id))
                    ->color('warning')
                    ->button(),
                // action akhir semester
                Action::make('full')
                    ->label('Akhir Semester')
                    ->size(ActionSize::Small)
                    ->color(Color::Pink)
                    ->url(fn($record) => route('report-full-semester', $record->student_id))
                    ->button(),
                // action project
                Action::make('project')
                    ->label('Project')
                    ->size(ActionSize::Small)
                    ->color(Color::Indigo)
                    ->url(fn($record) => route('report-project', $record->student_id))
                    ->button(),
                // action quran
                Action::make('quran')
                    ->label('Quran')
                    ->size(ActionSize::Small)
                    ->color(Color::Blue)
                    ->url(fn($record) => route('report-quran', $record->student_id))
                    ->button(),
            ])
            ->defaultSort('grade.name')
            ->paginated(false)
            ->modifyQueryUsing(fn(Builder $query) => $query->myGrade());
    }
}
