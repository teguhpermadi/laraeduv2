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

    public $teacherQuranGrade, 
            $students, 
            $competencyQuranCount, 
            $time_signature, 
            $preview, 
            $leger_quran, 
            $agree, 
            $quran_grade_id, 
            $academic_year_id,
            $teacher_quran_grade_id;
    public $checkLegerQuran = false;
    public $hasNoScores = false;
    public $descriptionLegerQuranRecap = '';

    // mount
    public function mount($id): void
    {
        $teacherQuranGrades = TeacherQuranGrade::with([
                'academicYear',
                'teacher',
                'quranGrade',
                'studentQuranGrade.student',
                'competencyQuran'
            ])->myQuranGrade()
            ->where('id', $id)->get();
        // dd($teacherQuranGrades->toArray());
        $this->teacherQuranGrade = $teacherQuranGrades->first();
        $this->competencyQuranCount = $teacherQuranGrades->sum(function ($teacherQuranGrade) {
            return $teacherQuranGrade->competencyQuran->count();
        });

        // dd($this->teacherQuranGrade->toArray());

        $students = $teacherQuranGrades->flatMap(function ($teacherQuranGrade) {
            return $teacherQuranGrade->studentQuranGrade->map(function ($studentQuranGrade) {
                return [
                    'student_id' => $studentQuranGrade->student->id,
                    'nis' => $studentQuranGrade->student->nis,
                    'name' => $studentQuranGrade->student->name,
                    // tambahkan jumlah seluruh score dari competency_quran_id, tulis dalam array 'sum_score'
                    'sum_score' => $studentQuranGrade->studentCompetencyQuran->sum('score'),
                    // tambahkan avg score dari competency_quran_id, tulis dalam array 'avg_score'
                    'avg_score' => round($studentQuranGrade->studentCompetencyQuran->avg('score'), 1),
                    // tambahkan deskripsi dari competency_quran_id, tulis dalam array 'description' gunakan description helper function
                    'description' => $this->getDescription($studentQuranGrade->studentCompetencyQuran),
                    'competencies' => $studentQuranGrade->studentCompetencyQuran
                        // urutkan berdasarkan competency_quran_id
                        ->sortBy('competency_quran_id')
                        // map dengan key competency_quran_id, berisi score & description
                        ->mapWithKeys(function ($competency) {
                            return [
                                $competency->competencyQuran->code => [
                                    // code competency_quran_id
                                    'code' => $competency->competencyQuran->code,
                                    'competency_quran_id' => $competency->competencyQuran->id,
                                    'score' => $competency->score,
                                    'description' => $competency->competencyQuran->description,
                                ],
                            ];
                        })
                        ->toArray(),
                ];
            });
        });

        // tambahkan ranking berdasarkan sum_score
        $students = $students->sortByDesc('sum_score')->values();

        // tambahkan ranking berdasarkan sum_score terbanyak
        $students = $students->map(function ($item, $index) {
            $item['ranking'] = $index + 1;
            return $item;
        });

        // kembalikan data sort by student_id asc
        $students = $students->sortBy('student_id', SORT_ASC);
        
        $this->students = $students;
        // dd($students->toArray());

        // cek apakah sudah ada data leger_quran
        $this->checkLegerQuran = ModelsLegerQuran::where('academic_year_id', $this->teacherQuranGrade->academicYear->id)
            ->where('quran_grade_id', $this->teacherQuranGrade->quran_grade_id)
            ->exists();

        // ambil data recap leger quran
        $legerQuranRecap = LegerQuranRecap::where('academic_year_id', $this->teacherQuranGrade->academicYear->id)
            ->where('teacher_quran_grade_id', $this->teacherQuranGrade->id)
            ->first();

        // dd($legerQuranRecap);

        if ($this->checkLegerQuran) {
            $descriptionLegerQuranRecap = 'Kamu sudah mengumpulkan leger ini ke wali kelas pada tanggal ' . $legerQuranRecap->created_at->translatedFormat('l, d F Y H:i') . '. Apakah kamu ingin mengrubahnya?';
        } else {
            $descriptionLegerQuranRecap = 'Apakah anda yakin akan mengumpulkan nilai tersebut ke wali kelas?';
        }

        $this->descriptionLegerQuranRecap = $descriptionLegerQuranRecap;

        // Cek apakah ada siswa yang belum memiliki nilai
        $this->hasNoScores = $students->contains(function ($item) {
            return empty($item['competencies']);
        });

        if (!$this->hasNoScores) {
            $this->form->fill([
                'teacher_quran_grade_id' => $teacherQuranGrades->first()->id,
                'academic_year_id' => $teacherQuranGrades->first()->academic_year_id,
                'quran_grade_id' => $teacherQuranGrades->first()->quran_grade_id,
                'leger_quran' => $students,
                'time_signature' => now(),
            ]);
        }

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
                        Hidden::make('quran_grade_id'),
                        Hidden::make('academic_year_id'),
                        Hidden::make('teacher_quran_grade_id'),
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

        foreach ($data['leger_quran'] as $leger_quran) {
            
            ModelsLegerQuran::updateOrCreate([
                'academic_year_id' => $data['academic_year_id'],
                'student_id' => $leger_quran['student_id'],
                'quran_grade_id' => $data['quran_grade_id'],
                'teacher_quran_grade_id' => $data['teacher_quran_grade_id'],
            ], [
                'score' => $leger_quran['avg_score'],
                'description' => $leger_quran['description'],
                'metadata' => $leger_quran['competencies'],
                'sum' => $leger_quran['sum_score'],
                'rank' => $leger_quran['ranking'],
            ]);
        }

        // leger quran recap
        LegerQuranRecap::updateOrCreate([
            'academic_year_id' => $data['academic_year_id'],
            'teacher_quran_grade_id' => $data['teacher_quran_grade_id'],
        ]);

        // refresh page to leger quran
        // $this->redirect(route('filament.pages.leger-quran', $this->teacherQuranGrade->id));

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
