<?php

namespace App\Filament\Pages;

use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\Student;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\ActionSize;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class Reports extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-bar';

    protected static string $view = 'filament.pages.reports';

    protected static ?string $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 10;

    public static function getNavigationLabel(): string
    {
        return 'Rapor Semua Siswa';
    }

    public static function getModelLabel(): string
    {
        return 'Rapor Semua Siswa';
    }

    public static function canAccess(array $parameters = []): bool
    {
        $user = Filament::auth()->user();

        if (! $user) {
            return false;
        }

        return $user->hasRole('super_admin');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Student::query()->with(['studentGradeFirst.grade', 'attendanceFirst']))
            ->columns([
                Stack::make([
                    TextColumn::make('nisn')
                        ->label('NISN')
                        ->sortable(),
                    TextColumn::make('name')
                        ->label('Nama Siswa')
                        ->wrap()
                        ->searchable()
                        ->sortable(),
                    TextColumn::make('studentGradeFirst.grade.name')
                        ->label('Kelas')
                        ->sortable(),
                ]),
                IconColumn::make('attendanceFirst.status')
                    ->label('Naik Kelas')
                    ->boolean()
                    ->hidden(
                        function () {
                            $academic = AcademicYear::find(session('academic_year_id'));

                            if ($academic->semester == 'ganjil') {
                                return true;
                            }

                            return false;
                        }
                    ),
            ])
            ->filters([
                SelectFilter::make('grade_id')
                    ->label('Kelas')
                    ->options(Grade::pluck('name', 'id'))
                    ->searchable()
                    ->query(function (Builder $query, $state) {
                        if ($state) {
                            $query->whereHas('studentGradeFirst', fn (Builder $q) => $q->where('grade_id', $state));
                        }
                    }),
            ])
            ->headerActions([
                Action::make('preview')
                    ->label('Leger Kelas')
                    ->url(fn () => route('leger-preview-my-grade'))
                    ->button(),
            ])
            ->actions([
                Action::make('cover')
                    ->label('Cover')
                    ->size(ActionSize::Small)
                    ->color(Color::Emerald)
                    ->url(fn ($record) => route('report-cover', $record->id))
                    ->button(),
                Action::make('identitas')
                    ->size(ActionSize::Small)
                    ->label('Identitas')
                    ->color(Color::Fuchsia)
                    ->url(fn ($record) => route('report-cover-student', $record->id))
                    ->button(),
                Action::make('middle')
                    ->label('Tengah Semester')
                    ->size(ActionSize::Small)
                    ->color(Color::Amber)
                    ->url(fn ($record) => route('report-half-semester', $record->id))
                    ->color('warning')
                    ->button(),
                Action::make('full')
                    ->label('Akhir Semester')
                    ->size(ActionSize::Small)
                    ->color(Color::Pink)
                    ->url(fn ($record) => route('report-full-semester', $record->id))
                    ->button(),
                Action::make('project')
                    ->label('Project')
                    ->size(ActionSize::Small)
                    ->color(Color::Indigo)
                    ->url(fn ($record) => route('report-project', $record->id))
                    ->button(),
                Action::make('quran')
                    ->label('Quran')
                    ->size(ActionSize::Small)
                    ->color(Color::Blue)
                    ->url(fn ($record) => route('report-quran', $record->id))
                    ->button(),
            ])
            ->defaultSort('name')
            ->paginated(false);
    }
}
