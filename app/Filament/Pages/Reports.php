<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AcademicYearSwitcher;
use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\Scopes\StudentActiveScope;
use App\Models\Student;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\ActionSize;
use Filament\Tables\Actions\Action;
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

    public static function canAccess(array $parameters = []): bool
    {
        $user = Filament::auth()->user();

        if (! $user) {
            return false;
        }

        return $user->hasRole(['super_admin', 'admin']);
    }

    public function mount(): void
    {
        if (! session()->has('academic_year_id')) {
            $academic = AcademicYear::first();

            if ($academic) {
                session()->put('academic_year_id', $academic->id);
            }
        }
    }

    protected function getHeaderWidgets(): array
    {
        return [AcademicYearSwitcher::class];
    }

    public function getHeaderWidgetsColumns(): int|string|array
    {
        return 1;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Student::query()
                    ->withoutGlobalScope(StudentActiveScope::class)
                    ->with(['studentGradeFirst.grade'])
                    ->whereHas('studentGradeFirst')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('studentGradeFirst.grade.name')
                    ->label('Kelas')
                    ->sortable(),
            ])
            ->defaultSort('studentGradeFirst.grade.name', 'asc')
            ->filters([
                SelectFilter::make('grade_id')
                    ->label('Kelas')
                    ->options(Grade::all()->pluck('name', 'id'))
                    ->query(fn (Builder $query, array $data) => $data['value']
                            ? $query->whereHas('studentGradeFirst', fn ($q) => $q->where('grade_id', $data['value']))
                            : $query
                    ),
            ])
            ->actions([
                Action::make('cover')
                    ->label('Cover')
                    ->size(ActionSize::Small)
                    ->color(Color::Emerald)
                    ->url(fn ($record) => route('report-cover', $record->id))
                    ->button(),
                Action::make('identitas')
                    ->label('Identitas')
                    ->size(ActionSize::Small)
                    ->color(Color::Fuchsia)
                    ->url(fn ($record) => route('report-cover-student', $record->id))
                    ->button(),
                Action::make('middle')
                    ->label('Tengah Semester')
                    ->size(ActionSize::Small)
                    ->color(Color::Amber)
                    ->url(fn ($record) => route('report-half-semester', $record->id))
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
            ]);
    }
}
