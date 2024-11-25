<?php

namespace App\Filament\Pages;

use App\Models\LegerQuran as ModelsLegerQuran;
use App\Models\LegerQuranRecap;
use App\Models\TeacherQuranGrade;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class LegerQuran extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.leger-quran';

    // slug
    protected static ?string $slug = 'leger-quran/{id}';

    // register navigation
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public ?array $data = [];

    public $teacherQuranGrade, $students, $time_signature, $preview, $student, $agree, $leger_quran, $competency_count, $academic_year_id;
    public $checkLegerQuran = false;
    public $hasNoScores = false;
    public $descriptionLegerQuranRecap = '';
    public $loading = false;

    // mount
    public function mount($id): void
    {
        
        $teacherQuranGrade = TeacherQuranGrade::with(['quranGrade',
            'studentQuranGrade',
            'competencyQuran.studentCompetencyQuran' => function ($query) {
                $query->orderBy('competency_quran_id', 'asc');
            },
        ])->find($id);

        // dd($teacherQuranGrade->toArray());

        $this->teacherQuranGrade = $teacherQuranGrade;
        $this->academic_year_id = $teacherQuranGrade->academicYear;
        $students = $this->teacherQuranGrade->studentQuranGrade;
        $competencyQuran = $this->teacherQuranGrade->competencyQuran;
        // dd($students->toArray());

        $this->competency_count = $competencyQuran->count();

        $data = collect();

        // dd($competencyQuran->toArray());
        
        // loop students
        foreach ($students as $student) {
            // urutkan berdasarkan competency_quran_id
            $studentCompetencyQuran = $student->studentCompetencyQuran()->orderBy('competency_quran_id', 'asc')->get();

            // deskripsi
            $description = $this->getDescription($student->studentCompetencyQuran);


            $data->push([
                'student_id' => $student->student->id,
                'student' => $student,
                'metadata' => $studentCompetencyQuran,
                'avg' => round($student->studentCompetencyQuran->avg('score'), 0),
                'sum' => $student->studentCompetencyQuran->sum('score'),
                'competency_count' => $this->competency_count,
                'description' => $description,
            ]);

            // dd($student->toArray());
        }

        // sort by sum
        $data = $data->sortByDesc('sum')->values();

        // add ranking
        $data = $data->map(function ($item, $index) {
            $item['rank'] = $index + 1;
            return $item;
        });

        // kembalikan data sort by id asc
        $data = $data->sortBy('student_id', SORT_ASC)->values();

        // dd($data->toArray());

        // Cek apakah ada siswa yang belum memiliki nilai
        $this->hasNoScores = $data->contains(function ($item) {
            return $item['competency_count'] === 0 || empty($item['metadata']);
        });

        $this->students = $data;

        // Hanya isi form jika ada nilai
        if (!$this->hasNoScores) {
            $this->form->fill([
                'leger_quran' => $data,
                'time_signature' => now(),
            ]);
        }

        // cek apakah sudah ada data leger_quran
        $this->checkLegerQuran = ModelsLegerQuran::where('academic_year_id', $this->academic_year_id)
            ->where('teacher_quran_grade_id', $id)
            ->exists();

        if ($this->checkLegerQuran) {
            $descriptionLegerQuranRecap = 'Kamu sudah mengumpulkan leger ini ke wali kelas pada tanggal ' . $this->checkLegerQuran->created_at->translatedFormat('l, d F Y H:i') . '. Apakah kamu ingin mengrubahnya?';
        } else {
            $descriptionLegerQuranRecap = 'Apakah anda yakin akan mengumpulkan nilai tersebut ke wali kelas?';
        }

        $this->descriptionLegerQuranRecap = $descriptionLegerQuranRecap;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Preview Leger')
                    ->schema([
                        ViewField::make('preview')
                            ->viewData([$this->teacherQuranGrade, $this->students])
                            ->view('filament.pages.leger-preview-quran'),
                    ])
                    ->collapsible(),
                Section::make('Persetujuan')
                    ->description($this->descriptionLegerQuranRecap)
                    ->schema([
                        Hidden::make('leger_quran'),
                        DateTimePicker::make('time_signature')
                            ->label('Waktu tanda tangan')
                            ->inlineLabel()
                            ->disabled(),
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
        // tambahkan loading state
        $this->loading = true;

        $data = $this->form->getState();

        $teacherQuranGrade = $this->teacherQuran;

        foreach ($data['leger_quran'] as $leger_quran) {
            ModelsLegerQuran::updateOrCreate([
                'academic_year_id' => session('academic_year_id'),
                'student_id' => $leger_quran['student_id'],
                'quran_grade_id' => $leger_quran['student']->quran_grade_id,
                'teacher_quran_grade_id' => $teacherQuranGrade->id,
            ], [
                'score' => $leger_quran['avg'],
                'description' => $leger_quran['description'],
                'metadata' => $leger_quran['metadata'],
                'sum' => $leger_quran['sum'],
                'rank' => $leger_quran['rank'],
            ]);
        }

        // leger quran recap
        LegerQuranRecap::updateOrCreate([
            'academic_year_id' => session('academic_year_id'),
            'teacher_quran_grade_id' => $teacherQuranGrade->id,
        ]);

        // tambahkan notifikasi sukses
        Notification::make()
            ->success()
            ->title('Berhasil menyimpan data')
            ->send();
    }

    public function getDescription($studentCompetencyQuran)
    {
        $string = '';

        // kelompokkan terlebih dahulu
        // competency lulus & tidak lulus
        $passed = 'Ananda telah menguasai materi: ';
        $notPassed = 'Ananda perlu peningkatan lagi pada materi materi: ';
        $countPassed = 0;
        $countNotPassed = 0;

        // dd($studentCompetencyQuran);

        foreach ($studentCompetencyQuran as $item) {
            if ($item->score >= $item->competencyQuran->passing_grade) {
                // jika lulus
                $passed .= $item->competencyQuran->description . '; ';
                $countPassed++;
            } else {
                // jika tidak lulus
                $notPassed .= $item->competencyQuran->description . '; ';
                $countNotPassed++;
            }
        }

        // cek jika ada isinya
        if ($countPassed > 0) {
            $string .= $passed;
        }

        if ($countNotPassed > 0) {
            $string .= $notPassed;
        }

        return $string;
    }
}
