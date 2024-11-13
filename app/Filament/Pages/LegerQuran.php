<?php

namespace App\Filament\Pages;

use App\Models\LegerQuran as ModelsLegerQuran;
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

    public $teacherQuran, $students, $time_signature, $preview, $student, $agree, $leger_quran, $competency_count, $academic_year_id;
    public $checkLegerRecap = false;
    public $hasNoScores = false;
    public $loading = false;

    // mount
    public function mount($id): void
    {
        $this->academic_year_id = session('academic_year_id');

        $this->teacherQuran = TeacherQuranGrade::with('quranGrade', 'studentQuranGrade', 'competencyQuran')->find($id);

        $students = $this->teacherQuran->studentQuranGrade;

        $this->competency_count = $this->teacherQuran->competencyQuran->count();

        $data = collect();

        // dd($students);

        // loop students
        foreach ($students as $student) {

            // deskripsi
            $description = $this->getDescription($student->studentCompetencyQuran);

            $data->push([
                'student_id' => $student->student->id,
                'student' => $student,
                'metadata' => $student->studentCompetencyQuran,
                'avg' => round($student->studentCompetencyQuran->avg('score'), 0),
                'sum' => $student->studentCompetencyQuran->sum('score'),
                'competency_count' => $this->competency_count,
                'description' => $description,
            ]);
        }

        // sort by sum
        $data = $data->sortByDesc('sum')->values();

        // add ranking
        $data = $data->map(function ($item, $index) {
            $item['rank'] = $index + 1;
            return $item;
        });

        // kembalikan data sort by id asc
        $data = $data->sortBy('student_id')->values();

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
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Preview')
                    ->schema([
                        ViewField::make('preview')
                            ->viewData([$this->teacherQuran, $this->students])
                            ->view('filament.pages.leger-preview-quran'),
                    ]),
                Section::make('Persetujuan')
                    ->description('Apakah anda setuju dengan seluruh nilai tersebut?')
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
            ],[
                'score' => $leger_quran['avg'],
                'description' => $leger_quran['description'],
                'metadata' => $leger_quran['metadata'],
                'sum' => $leger_quran['sum'],
                'rank' => $leger_quran['rank'],
            ]);
        }
        
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
