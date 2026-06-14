<?php

namespace App\Filament\Pages;

use App\Enums\CategoryLegerEnum;
use App\Models\Leger as ModelsLeger;
use App\Models\LegerRecap;
use App\Models\TeacherSubject;
use App\Services\LegerCalculationService;
use App\Services\LegerSubmitService;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class Leger extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $slug = 'leger/{id}';

    protected static string $view = 'filament.pages.leger';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public ?array $data = [];

    public $teacherGrade;

    public $teacherSubject;

    public $students;

    public $competencyCount;

    public $time_signature;

    public $preview_full_semester;

    public $preview_half_semester;

    public $leger;

    public $agree;

    public $grade_id;

    public $academic_year_id;

    public $teacher_subject_id;

    public $leger_full_semester;

    public $leger_half_semester;

    public $competenciesFullSemester;

    public $competenciesHalfSemester;

    public $studentsFullSemester;

    public $studentsHalfSemester;

    public $checkLegerRecap = false;

    public $hasNoScores = false;

    public $descriptionLegerRecap = '';

    public $visible = false;

    public function mount($id): void
    {
        $teacherSubject = TeacherSubject::with([
            'teacher',
            'subject',
            'academic',
            'competency',
            'studentGrade.studentCompetency.competency',
        ])->find($id);

        $this->teacherSubject = $teacherSubject;
        $this->competenciesFullSemester = $teacherSubject->competency;
        $this->competenciesHalfSemester = $teacherSubject->competency->where('half_semester', true);

        $calculator = app(LegerCalculationService::class);

        $this->studentsFullSemester = $calculator->buildStudentsData(
            $teacherSubject->studentGrade,
            $this->competenciesFullSemester,
            $teacherSubject,
            CategoryLegerEnum::FULL_SEMESTER->value
        );

        $this->studentsHalfSemester = $calculator->buildStudentsData(
            $teacherSubject->studentGrade,
            $this->competenciesHalfSemester,
            $teacherSubject,
            CategoryLegerEnum::HALF_SEMESTER->value
        );

        $legerRecap = LegerRecap::where('academic_year_id', $teacherSubject->academic->id)
            ->where('teacher_subject_id', $teacherSubject->id)
            ->first();

        $this->checkLegerRecap = (bool) $legerRecap;

        $this->descriptionLegerRecap = $legerRecap
            ? 'Kamu sudah mengumpulkan leger ini ke wali kelas pada tanggal '.$legerRecap->created_at->translatedFormat('l, d F Y H:i').'. Apakah kamu ingin mengrubahnya?'
            : 'Apakah anda yakin akan mengumpulkan nilai tersebut ke wali kelas?';

        $this->hasNoScores = $calculator->hasNoScores($this->studentsFullSemester);

        if (! $this->hasNoScores) {
            $this->form->fill([
                'teacher_subject_id' => $id,
                'academic_year_id' => $teacherSubject->academic->id,
                'leger_full_semester' => $this->studentsFullSemester,
                'leger_half_semester' => $this->studentsHalfSemester,
                'time_signature' => now(),
            ]);
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // section half semester
                Section::make('Preview Tengah Semester')
                    ->schema([
                        ViewField::make('preview_half_semester')
                            ->view('filament.pages.leger-preview')
                            ->viewData([
                                'teacherSubject' => $this->teacherSubject,
                                'competencies' => $this->competenciesHalfSemester,
                                'students' => $this->studentsHalfSemester,
                            ]),
                    ])
                    ->collapsible(),
                // section full semester
                Section::make('Preview Akhir Semester')
                    ->schema([
                        ViewField::make('preview_full_semester')
                            // masukkan data teacherSubject dan leger_full_semester
                            ->view('filament.pages.leger-preview')
                            ->viewData([
                                'teacherSubject' => $this->teacherSubject,
                                'competencies' => $this->competenciesFullSemester,
                                'students' => $this->studentsFullSemester,
                            ]),
                    ])
                    ->collapsible(),

                Section::make('Persetujuan')
                    ->description(fn () => $this->descriptionLegerRecap)
                    ->schema([
                        Hidden::make('teacher_subject_id'),
                        Hidden::make('academic_year_id'),
                        Hidden::make('leger_full_semester'),
                        Hidden::make('leger_half_semester'),
                        DateTimePicker::make('time_signature')
                            ->label('Waktu tanda tangan')
                            ->inlineLabel(),
                        Checkbox::make('agree')
                            ->label('Saya setuju')
                            ->inlineLabel()
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public function submit()
    {
        $data = $this->form->getState();

        app(LegerSubmitService::class)->submit($data);

        $this->redirect(route('filament.admin.pages.leger.{id}', ['id' => $this->teacherSubject->id]));

        Notification::make()
            ->title('Berhasil')
            ->body('Leger berhasil disimpan')
            ->success()
            ->send();
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading('Catatan Guru')
            ->description('Catatan guru akan tampil di rapor siswa')
            ->query(ModelsLeger::query())
            ->columns([
                TextColumn::make('student.name'),
                TextColumn::make('category')
                    ->badge(),
                // ->color(fn (string $state): string => match ($state) {
                //     CategoryLegerEnum::HALF_SEMESTER->value => 'warning',
                //     CategoryLegerEnum::FULL_SEMESTER->value => 'primary',
                // }),
                TextInputColumn::make('note.note'),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options(CategoryLegerEnum::class)
                    ->default(CategoryLegerEnum::FULL_SEMESTER->value),
            ])
            ->headerActions([
                // make reset action
                Action::make('reset')
                    ->label('Reset Catatan')
                    ->color('danger')
                    ->icon('heroicon-o-arrow-path-rounded-square')
                    ->action(function () {
                        // ambil semua leger yang teacher_subject_id sama dengan teacherSubject id
                        $legers = ModelsLeger::where('teacher_subject_id', $this->teacherSubject->id)->get();
                        // dd($leger->toArray());
                        foreach ($legers as $item) {
                            $item->note()->updateOrCreate(['leger_id' => $item->id], ['note' => '-']);
                        }
                    }),
            ])
            ->bulkActions([
                BulkAction::make('catatan')
                    ->label('Buat Catatan')
                    ->form([
                        Textarea::make('note')
                            ->label('Catatan')
                            ->required(),
                    ])
                    ->action(function (Collection $records, array $data) {
                        foreach ($records as $record) {
                            $record->note()->updateOrCreate(['leger_id' => $record->id], ['note' => $data['note']]);
                        }
                    }),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query->where('teacher_subject_id', $this->teacherSubject->id);
            });
    }
}
