<?php

namespace App\Filament\Pages;

use App\Models\Extracurricular;
use App\Models\StudentExtracurricular;
use App\Models\TeacherExtracurricular;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;

class AssessmentExtracurricular extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.assessment-extracurricular';

    public ?array $data = [];
    public $teacherExtracurricular;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Assessment Extrakurikuler')
                ->schema([
                    // tambilkan select extracurricular
                    Hidden::make('academic_year_id')
                        ->default(session('academic_year_id')),
                    // buatkan radio untuk menampilakan extracurricular
                    Radio::make('extracurricular_id')
                        ->label('Extrakurikuler')
                        ->options(function () {
                            // tampilkan extracurricular yang dimiliki oleh teacher yang login
                            $teacher_id = auth()->user()->userable->userable_id;

                            $teacherExtracurricular = TeacherExtracurricular::with('extracurricular')
                                ->where('teacher_id', $teacher_id)
                                ->where('academic_year_id', session('academic_year_id'))
                                ->get();

                            return $teacherExtracurricular->pluck('extracurricular.name', 'extracurricular.id');
                        })
                        ->live()
                        ->afterStateUpdated(function () {
                            $this->resetTable();
                        }),
                ])
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(StudentExtracurricular::query())
            ->columns([
                TextColumn::make('student.name'),
                TextColumn::make('extracurricular.name'),
                TextColumn::make('score'),
            ])
            ->headerActions([
                Action::make('reset')
                    ->icon('heroicon-s-arrow-path-rounded-square')
                    ->action(function () {
                        // $this->resetStudentCompetency($this->teacher_subject_id);
                    })
                    ->button(),
            ]);
    }

    public function resetTable(): void
    {
        $this->table->query(StudentExtracurricular::query());
    }
}
