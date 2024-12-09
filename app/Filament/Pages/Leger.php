<?php

namespace App\Filament\Pages;

use App\Enums\CategoryLegerEnum;
use App\Helpers\DescriptionHelper;
use App\Models\Leger as ModelsLeger;
use App\Models\LegerNote;
use App\Models\LegerRecap;
use App\Models\TeacherSubject;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
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

    public $teacherGrade,
        $teacherSubject,
        $students,
        $competencyCount,
        $time_signature,
        $preview_full_semester,
        $preview_half_semester,
        $leger,
        $agree,
        $grade_id,
        $academic_year_id,
        $teacher_subject_id,
        $leger_full_semester,
        $leger_half_semester,
        $competenciesFullSemester,
        $competenciesHalfSemester,
        $studentsFullSemester,
        $studentsHalfSemester;
    public $checkLegerRecap = false;
    public $hasNoScores = false;
    public $descriptionLegerRecap = '';

    public $visible = false;

    public function mount($id): void
    {
        // ambil data teacher_subject
        $teacherSubject = TeacherSubject::with([
            'teacher',
            'subject',
            'academic',
            'competency',
            'studentGrade'
        ])->find($id);

        $this->teacherSubject = $teacherSubject;
        $teacher_id = $teacherSubject->teacher_id;
        $subject_id = $teacherSubject->subject_id;

        // ambil data competency berdasarkan teacher_subject_id
        $competenciesFullSemester = $teacherSubject->competency;
        $competenciesHalfSemester = $teacherSubject->competency->where('half_semester', true);

        // subject order
        $subjectOrder = $teacherSubject->subject->order;

        $this->competenciesFullSemester = $competenciesFullSemester;
        $this->competenciesHalfSemester = $competenciesHalfSemester;

        // dd($competenciesHalfSemester->toArray());

        // flat map full semester
        $studentsFullSemester = $teacherSubject->studentGrade->map(function ($student) use ($competenciesFullSemester, $subjectOrder, $teacher_id, $subject_id) {
            // buat description dengan description helper
            $description = DescriptionHelper::getDescription($student->studentCompetency->whereIn('competency_id', $competenciesFullSemester->pluck('id')));
            $avg_score = $student->studentCompetency->whereIn('competency_id', $competenciesFullSemester->pluck('id'))->avg('score');
            $avg_skill = $student->studentCompetency->whereIn('competency_id', $competenciesFullSemester->pluck('id'))->avg('score_skill');
            $sum_score = $student->studentCompetency->whereIn('competency_id', $competenciesFullSemester->pluck('id'))->sum('score');
            $sum_skill = $student->studentCompetency->whereIn('competency_id', $competenciesFullSemester->pluck('id'))->sum('score_skill');
            // passing grade adalah rata-rata dari passing grade competency
            $passing_grade = $competenciesFullSemester->avg('passing_grade');

            return [
                'student_id' => $student->student->id,
                'teacher_id' => $teacher_id,    
                'subject_id' => $subject_id,
                'subject_order' => $subjectOrder,
                'nis' => $student->student->nis,
                'name' => $student->student->name,
                'avg_score' => round($avg_score, 0),
                'avg_skill' => round($avg_skill, 0),
                'sum_score' => $sum_score,
                'sum_skill' => $sum_skill,
                'description' => $description['description'],
                'description_skill' => $description['description_skill'],
                'competency_count' => $competenciesFullSemester->count(),
                'passing_grade' => round($passing_grade, 0),
                'competencies' => $student->studentCompetency
                    // ambil data competency berdasarkan competencies yang ada di $competencies
                    ->whereIn('competency_id', $competenciesFullSemester->pluck('id'))
                    ->sortBy('competency_id')
                    ->map(function ($competency) {
                        return [
                            'competency_id' => $competency->competency_id,
                            'code' => $competency->competency->code,
                            'score' => $competency->score,
                            'passing_grade' => $competency->competency->passing_grade,
                            'description' => $competency->competency->description,
                            'score_skill' => $competency->score_skill,
                            'description_skill' => $competency->competency->description_skill,
                        ];
                    }),
               
            ];
        });

        // tambahkan ranking berdasarkan sum_score
        $studentsFullSemester = $studentsFullSemester->sortByDesc('sum_score')->values();

        // tambahkan ranking berdasarkan sum_score
        $studentsFullSemester = $studentsFullSemester->map(function ($item, $index) {
            $item['ranking'] = $index + 1;
            return $item;
        });

        // kembalikan data sort by student_id asc
        $studentsFullSemester = $studentsFullSemester->sortBy('student_id', SORT_ASC);
        $this->studentsFullSemester = $studentsFullSemester;

        // flat map half semester
        $studentsHalfSemester = $teacherSubject->studentGrade->map(function ($student) use ($competenciesHalfSemester, $subjectOrder, $teacher_id, $subject_id) {
            $description = DescriptionHelper::getDescription($student->studentCompetency->whereIn('competency_id', $competenciesHalfSemester->pluck('id')));
            $avg_score = $student->studentCompetency->whereIn('competency_id', $competenciesHalfSemester->pluck('id'))->avg('score');
            $sum_score = $student->studentCompetency->whereIn('competency_id', $competenciesHalfSemester->pluck('id'))->sum('score');
            $avg_skill = $student->studentCompetency->whereIn('competency_id', $competenciesHalfSemester->pluck('id'))->avg('score_skill');
            $sum_skill = $student->studentCompetency->whereIn('competency_id', $competenciesHalfSemester->pluck('id'))->sum('score_skill');
            $passing_grade = $competenciesHalfSemester->avg('passing_grade');
            return [
                'student_id' => $student->student->id,
                'teacher_id' => $teacher_id,
                'subject_id' => $subject_id,
                'subject_order' => $subjectOrder,
                'nis' => $student->student->nis,
                'name' => $student->student->name,
                'avg_score' => round($avg_score, 0),
                'avg_skill' => round($avg_skill, 0),
                'sum_score' => $sum_score,
                'sum_skill' => $sum_skill,
                'description' => $description['description'],
                'description_skill' => $description['description_skill'],
                'competency_count' => $competenciesHalfSemester->count(),
                'passing_grade' => round($passing_grade, 0),
                'competencies' => $student->studentCompetency
                    ->whereIn('competency_id', $competenciesHalfSemester->pluck('id'))
                    ->sortBy('competency_id')
                    ->map(function ($competency) {
                        return [
                            'competency_id' => $competency->competency_id,
                            'code' => $competency->competency->code,
                            'score' => $competency->score,
                            'passing_grade' => $competency->competency->passing_grade,
                            'description' => $competency->competency->description,
                            'score_skill' => $competency->score_skill,
                            'description_skill' => $competency->competency->description_skill,
                        ];
                    }),
            ];
        });

        // tambahkan ranking berdasarkan sum_score
        $studentsHalfSemester = $studentsHalfSemester->sortByDesc('sum_score')->values();

        // tambahkan ranking berdasarkan sum_score
        $studentsHalfSemester = $studentsHalfSemester->map(function ($item, $index) {
            $item['ranking'] = $index + 1;
            return $item;
        });

        // kembalikan data sort by student_id asc
        $studentsHalfSemester = $studentsHalfSemester->sortBy('student_id', SORT_ASC);

        $this->studentsHalfSemester = $studentsHalfSemester;

        // ambil data leger recap
        $legerRecap = LegerRecap::where('academic_year_id', $teacherSubject->academic->id)
            ->where('teacher_subject_id', $teacherSubject->id)
            ->first();

        $this->checkLegerRecap = $legerRecap ? true : false;
        // dd($legerRecap);
        if ($legerRecap) {
            $this->descriptionLegerRecap = 'Kamu sudah mengumpulkan leger ini ke wali kelas pada tanggal ' . $legerRecap->created_at->translatedFormat('l, d F Y H:i') . '. Apakah kamu ingin mengrubahnya?';
        } else {
            $this->descriptionLegerRecap = 'Apakah anda yakin akan mengumpulkan nilai tersebut ke wali kelas?';
        }

        // cek apakah ada siswa yang belum memiliki nilai
        $this->hasNoScores = $studentsFullSemester->contains(function ($item) {
            return empty($item['competencies']);
        });

        if (!$this->hasNoScores) {
            $this->form->fill([
                'teacher_subject_id' => $id,
                'academic_year_id' => $teacherSubject->academic->id,
                'leger_full_semester' => $studentsFullSemester,
                'leger_half_semester' => $studentsHalfSemester,
                'time_signature' => now(),
            ]);
        }

        // dd($this->leger_full_semester);

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
                            ])
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
                            ])
                    ])
                    ->collapsible(),
                
                Section::make('Persetujuan')
                    ->description(fn() => $this->descriptionLegerRecap)
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
                    ->columns(2)
            ]);
    }

    public function submit()
    {
        $data = $this->form->getState();

        // dd($data);

        /* FULL SEMESTER */
        // insert data ke table leger
        foreach ($data['leger_full_semester'] as $student) {
            $legerFullSemester = ModelsLeger::updateOrCreate([
                'academic_year_id' => $data['academic_year_id'],
                'student_id' => $student['student_id'],
                'teacher_subject_id' => $data['teacher_subject_id'],
                'teacher_id' => $student['teacher_id'],
                'subject_id' => $student['subject_id'],
                'category' => CategoryLegerEnum::FULL_SEMESTER->value,
            ], [
                'passing_grade' => $student['passing_grade'],
                'score' => $student['avg_score'],
                'score_skill' => $student['avg_skill'],
                'sum' => $student['sum_score'],
                'sum_skill' => $student['sum_skill'],
                'rank' => $student['ranking'],
                'description' => $student['description'],
                'description_skill' => $student['description_skill'],
                'metadata' => $student['competencies'],
                'subject_order' => $student['subject_order'],
            ]);

            // insert data ke table leger_note
            LegerNote::updateOrCreate([
                'leger_id' => $legerFullSemester->id,
            ], [
                'note' => '-',
            ]);
        }

        // insert data ke table leger_recap
        LegerRecap::updateOrCreate([
            'academic_year_id' => $data['academic_year_id'],
            'teacher_subject_id' => $data['teacher_subject_id'],
            'category' => CategoryLegerEnum::FULL_SEMESTER->value,
        ], [
            'updated_at' => $data['time_signature'],
        ]);

        /* HALF SEMESTER */
        foreach ($data['leger_half_semester'] as $student) {
            // insert data ke table leger
            $legerHalfSemester = ModelsLeger::updateOrCreate([
                'academic_year_id' => $data['academic_year_id'],
                'student_id' => $student['student_id'],
                'teacher_subject_id' => $data['teacher_subject_id'],
                'teacher_id' => $student['teacher_id'],
                'subject_id' => $student['subject_id'],
                'category' => CategoryLegerEnum::HALF_SEMESTER->value,
            ], [
                'passing_grade' => $student['passing_grade'],
                'score' => $student['avg_score'],
                'score_skill' => $student['avg_skill'],
                'sum' => $student['sum_score'],
                'sum_skill' => $student['sum_skill'],
                'rank' => $student['ranking'],
                'description' => $student['description'],
                'description_skill' => $student['description_skill'],
                'metadata' => $student['competencies'],
                'subject_order' => $student['subject_order'],
            ]);

            // insert data ke table leger_note
            LegerNote::updateOrCreate([
                'leger_id' => $legerHalfSemester->id,
            ], [
                'note' => '-',
            ]);
        }

        // insert data ke table leger_recap
        LegerRecap::updateOrCreate([
            'academic_year_id' => $data['academic_year_id'],
            'teacher_subject_id' => $data['teacher_subject_id'],
            'category' => CategoryLegerEnum::HALF_SEMESTER->value,
        ]);

        // refresh page
        $this->redirect(route('filament.admin.pages.leger.{id}', ['id' => $this->teacherSubject->id]));

        // notifikasi
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
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        CategoryLegerEnum::HALF_SEMESTER->value => 'warning',
                        CategoryLegerEnum::FULL_SEMESTER->value => 'primary',
                    }),
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
                    })
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
                    })
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query->where('teacher_subject_id', $this->teacherSubject->id);
            });
    }
}
