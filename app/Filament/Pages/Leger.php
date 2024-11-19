<?php

namespace App\Filament\Pages;

use App\Enums\CategoryLegerEnum;
use App\Enums\CurriculumEnum;
use App\Models\Leger as ModelsLeger;
use App\Models\LegerRecap;
use App\Models\StudentCompetency;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class Leger extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $slug = 'leger/{id}';

    protected static string $view = 'filament.pages.leger';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public ?array $data = [];

    public $leger_full_semester;
    public $leger_half_semester;

    public $teacherSubject, $students, $time_signature, $preview, $student, $agree, $leger, $competency_count, $academic_year_id;
    public $checkLegerRecap = false;
    public $hasNoScores = false;

    public $visible = false;

    public function mount($id): void
    {
        /* FULL SEMESTER */
        $competency = TeacherSubject::with('subject')->withCount('competency')->find($id);

        $this->teacherSubject = $competency;
        $this->competency_count = $competency->competency_count;
        $this->academic_year_id = $competency->academic_year_id;

        // ambil competency id full semester
        $competency_id_full = $competency->competency->pluck('id');

        // full semester
        $teacherSubjectFullSemester = TeacherSubject::with(['studentGrade.studentCompetency' => function ($query) use ($competency_id_full) {
            $query->whereIn('competency_id', $competency_id_full);
        }])->find($id);

        $dataFullSemester = collect();

        // all competency from full semester
        foreach ($teacherSubjectFullSemester->studentGrade as $studentGrade) {

            // deskripsi
            $description = $this->getDescription($studentGrade->studentCompetency);

            $dataFullSemester[$studentGrade->student_id] = collect([
                'academic_year_id' => $competency->academic_year_id,
                'teacher_subject_id' => $id,
                'student_id' => $studentGrade->student_id,
                'student' => $studentGrade->student,
                'competency_count' => count($studentGrade->studentCompetency),
                'avg' => round($studentGrade->studentCompetency->avg('score'), 0),
                'avg_skill' => round($studentGrade->studentCompetency->avg('score_skill'), 0),
                'sum' => $studentGrade->studentCompetency->sum('score'),
                'sum_skill' => $studentGrade->studentCompetency->sum('score_skill'),
                'passing_grade' => $teacherSubjectFullSemester->passing_grade,
                'metadata' => $studentGrade->studentCompetency,
                'description' => $description['description'],
                'description_skill' => $description['description_skill'],
                'subject_order' => $competency->subject->order,
            ]);
        };

        // Sort data by 'sum' in descending order
        $dataFullSemester = $dataFullSemester->sortByDesc('sum')->values();

        // Add ranking
        $dataFullSemester = $dataFullSemester->map(function ($item, $index) {
            $item['rank'] = $index + 1; // Rank starts from 1
            return $item;
        });

        // kembalikan data sort by id
        $dataFullSemester = $dataFullSemester->sortByDesc('student_id')->values();

        // Cek apakah ada siswa yang belum memiliki nilai
        $this->hasNoScores = $dataFullSemester->contains(function ($item) {
            return $item['competency_count'] === 0 || empty($item['metadata']);
        });

        /* HALF SEMESTER */

        // ambil competency id half semester
        $competency_id_half = $competency->competency->where('half_semester', 1)->pluck('id');

        // half semester
        $teacherSubjectHalfSemester = TeacherSubject::with(['studentGrade.studentCompetency' => function ($query) use ($competency_id_half) {
            $query->whereIn('competency_id', $competency_id_half);
        }])->find($id);

        $dataHalfSemester = collect();

        // all competency from half semester
        foreach ($teacherSubjectHalfSemester->studentGrade as $studentGrade) {
            $description = $this->getDescription($studentGrade->studentCompetency);

            $dataHalfSemester[$studentGrade->student_id] = collect([
                'academic_year_id' => $competency->academic_year_id,
                'teacher_subject_id' => $id,
                'student_id' => $studentGrade->student_id,
                'student' => $studentGrade->student,
                'competency_count' => count($studentGrade->studentCompetency),
                'avg' => round($studentGrade->studentCompetency->avg('score'), 0),
                'avg_skill' => round($studentGrade->studentCompetency->avg('score_skill'), 0),
                'sum' => $studentGrade->studentCompetency->sum('score'),
                'sum_skill' => $studentGrade->studentCompetency->sum('score_skill'),
                'passing_grade' => $teacherSubjectHalfSemester->passing_grade,
                'metadata' => $studentGrade->studentCompetency,
                'description' => $description['description'],
                'description_skill' => $description['description_skill'],
                'subject_order' => $competency->subject->order,
            ]);
        }       

        // Sort data by 'sum' in descending order
        $dataHalfSemester = $dataHalfSemester->sortByDesc('sum')->values();

        // Add ranking
        $dataHalfSemester = $dataHalfSemester->map(function ($item, $index) {
            $item['rank'] = $index + 1; // Rank starts from 1
            return $item;
        });

        // kembalikan data sort by id
        $dataHalfSemester = $dataHalfSemester->sortByDesc('student_id')->values();

        // dd($dataFullSemester);

        $this->students = $dataFullSemester;

        // Hanya isi form jika ada nilai
        if (!$this->hasNoScores) {
            $this->form->fill([
                'leger_full_semester' => $dataFullSemester,
                'leger_half_semester' => $dataHalfSemester,
                'time_signature' => now(),
            ]);
        }

        // cek apakah sudah ada data leger_recap
        $this->checkLegerRecap = LegerRecap::where('academic_year_id', $this->academic_year_id)
            ->where('teacher_subject_id', $id)
            ->exists();

        // cek kurikulum
        $curriculum = $competency->teacherGrade->curriculum;
        // dd($curriculum);

        if($curriculum == CurriculumEnum::K13->value){
            $this->visible = true;
        } else {
            $this->visible = false;
        }

        // dd($this->visible);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Preview')
                    ->schema([
                        ViewField::make('preview')
                            ->viewData([$this->teacherSubject, $this->students])
                            ->view('filament.pages.leger-preview'),
                    ]),
                Section::make('Persetujuan')
                    ->description('Apakah anda setuju dengan seluruh nilai tersebut?')
                    ->schema([
                        Hidden::make('leger_full_semester'),
                        Hidden::make('leger_half_semester'),
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
        $data = $this->form->getState();

        // dd($data);

        /* FULL SEMESTER */
        // insert data ke table leger
        foreach ($data['leger_full_semester'] as $key) {
            ModelsLeger::updateOrCreate([
                'academic_year_id' => $key['academic_year_id'],
                'student_id' => $key['student_id'],
                'teacher_subject_id' => $key['teacher_subject_id'],
                'category' => CategoryLegerEnum::FULL_SEMESTER->value,
            ], [
                'score' => $key['avg'],
                'score_skill' => $key['avg_skill'],
                'sum' => $key['sum'],
                'sum_skill' => $key['sum_skill'],
                'rank' => $key['rank'],
                'description' => $key['description'],
                'description_skill' => $key['description_skill'],
                'metadata' => $key['metadata'],
                'subject_order' => $key['subject_order'],
            ]);
        }

        // insert data ke table leger_recap
        LegerRecap::updateOrCreate([
            'academic_year_id' => $this->academic_year_id,
            'teacher_subject_id' => $this->teacherSubject->id,
            'category' => CategoryLegerEnum::FULL_SEMESTER->value,
        ]); 

        /* HALF SEMESTER */
        foreach ($data['leger_half_semester'] as $key) {
            // insert data ke table leger
            ModelsLeger::updateOrCreate([
                'academic_year_id' => $key['academic_year_id'],
                'student_id' => $key['student_id'],
                'teacher_subject_id' => $key['teacher_subject_id'],
                'category' => CategoryLegerEnum::HALF_SEMESTER->value,
            ], [
                'score' => $key['avg'],
                'score_skill' => $key['avg_skill'],
                'sum' => $key['sum'],
                'sum_skill' => $key['sum_skill'],
                'rank' => $key['rank'],
                'description' => $key['description'],
                'description_skill' => $key['description_skill'],
                'metadata' => $key['metadata'],
                'subject_order' => $key['subject_order'],
            ]);
        }   

        // insert data ke table leger_recap
        LegerRecap::updateOrCreate([
            'academic_year_id' => $this->academic_year_id,
            'teacher_subject_id' => $this->teacherSubject->id,
            'category' => CategoryLegerEnum::HALF_SEMESTER->value,
        ]);

        // notifikasi
        Notification::make()
            ->title('Berhasil')
            ->body('Leger berhasil disimpan')
            ->success()
            ->send();

        
    }

    public function getDescription($data)
    {
        $string = '';
        $string_skill = '';

        // hapus data tengah semester dan akhir semester
        // unset($data[0], $data[1]);

        // Asumsikan $data adalah array atau Collection yang berisi metadata
        $filter = collect($data)->reject(function ($item) {
            $code = strtolower($item['competency']['code']);
            return $code === 'tengah semester' || $code === 'akhir semester';
        })->values(); // Gunakan values() untuk reset indeks

        // kelompokkan terlebih dahulu
        // competency lulus & tidak lulus
        $passed = 'Ananda telah menguasai materi: ';
        $notPassed = 'Ananda perlu peningkatan lagi pada materi: ';
        $countPassed = 0;
        $countNotPassed = 0;

        $passedSkill = 'Ananda telah menguasai keterampilan: ';
        $notPassedSkill = 'Ananda perlu peningkatan lagi pada keterampilan: ';
        $countPassedSkill = 0;
        $countNotPassedSkill = 0;

        foreach ($filter as $competency) {
            if ($competency->score >= $competency->competency->passing_grade) {
                // jika lulus
                $passed .= $competency->competency->description . '; ';
                $countPassed++;
            } else {
                // jika tidak lulus
                $notPassed .= $competency->competency->description . '; ';
                $countNotPassed++;
            }

            // score skill
            if ($competency->score_skill >= $competency->competency->passing_grade) {
                // jika lulus
                $passedSkill .= $competency->competency->description_skill . '; ';
                $countPassedSkill++;
            } else {
                // jika tidak lulus
                $notPassedSkill .= $competency->competency->description_skill . '; ';
                $countNotPassedSkill++;
            }
        }

        // cek jika ada isinya
        if ($countPassed > 0) {
            $string .= $passed;
        }

        if ($countNotPassed > 0) {
            $string .= $notPassed;
        }

        // cek jika skill ada isinya
        if ($countPassed > 0) {
            $string_skill .= $passedSkill;
        }

        if ($countNotPassed > 0) {
            $string_skill .= $notPassedSkill;
        }

        return [
            'description' => $string,
            'description_skill' => $string_skill,
        ];
    }
}
