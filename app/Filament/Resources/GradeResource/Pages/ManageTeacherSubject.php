<?php

namespace App\Filament\Resources\GradeResource\Pages;

use App\Filament\Resources\GradeResource;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherSubject;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class ManageTeacherSubject extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string $resource = GradeResource::class;

    protected static string $view = 'filament.resources.grade-resource.pages.manage-teacher-subject';

    public $record;

    public ?array $data = [];

    public function mount($record): void
    {
        $this->record = $record;
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Repeater::make('subjects')
                    ->schema([
                        Select::make('teacher_id')
                            ->label('Guru')
                            ->options(Teacher::all()->pluck('name', 'id'))
                            ->required()
                            ->searchable(),
                        Select::make('subject_id')
                            ->label('Mata Pelajaran')
                            ->options(Subject::all()->pluck('name', 'id'))
                            ->required()
                            ->searchable(),
                        TextInput::make('time_allocation')
                            ->label('Alokasi Waktu')
                            ->numeric()
                            ->default(0)
                            ->required(),
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
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $created = 0;
        $skipped = 0;

        foreach ($data['subjects'] as $item) {
            $exists = TeacherSubject::where([
                'academic_year_id' => session('academic_year_id'),
                'teacher_id' => $item['teacher_id'],
                'subject_id' => $item['subject_id'],
                'grade_id' => $this->record,
            ])->exists();

            if ($exists) {
                $skipped++;

                continue;
            }

            TeacherSubject::create([
                'academic_year_id' => session('academic_year_id'),
                'teacher_id' => $item['teacher_id'],
                'subject_id' => $item['subject_id'],
                'grade_id' => $this->record,
                'time_allocation' => $item['time_allocation'],
                'passing_grade' => $item['passing_grade'] ?? 70,
            ]);
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
        } else {
            Notification::make()
                ->title('Semua data sudah ada, tidak ada yang disimpan')
                ->danger()
                ->send();
        }

        $this->form->fill();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(TeacherSubject::where('grade_id', $this->record))
            ->columns([
                TextColumn::make('teacher.name')
                    ->label('Guru')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('subject.name')
                    ->label('Mata Pelajaran')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('time_allocation')
                    ->label('Alokasi Waktu'),
                TextColumn::make('passing_grade')
                    ->label('KKM'),
            ])
            ->actions([
                DeleteAction::make(),
            ])
            ->paginated(false);
    }
}
