<?php

namespace App\Filament\Pages;

use App\Models\Leger as ModelsLeger;
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

    public $teacherSubject, $students, $time_signature, $preview, $student, $agree, $leger, $competency_count;

    public function mount($id): void
    {
        $competency = TeacherSubject::with('subject')->withCount('competency')->find($id);
        $this->teacherSubject = $competency;
        $this->competency_count = $competency->competency_count;

        $competency_id = $competency->competency->pluck('id');

        $teacherSubject = TeacherSubject::with(['studentGrade.studentCompetency' => function ($query) use ($competency_id) {
            $query->whereIn('competency_id', $competency_id);
        }])->find($id);

        $data = collect();

        foreach ($teacherSubject->studentGrade as $studentGrade) {

            // deskripsi
            $description = $this->getDescription($studentGrade->studentCompetency);
            $metadata = $studentGrade->studentCompetency;

            $data[$studentGrade->student_id] = collect([
                'academic_year_id' => $competency->academic_year_id,
                // 'grade_id' => $competency->grade_id,
                // 'teacher_id' => $competency->teacher_id,
                // 'subject_id' => $competency->subject_id,
                'teacher_subject_id' => $id,
                'student_id' => $studentGrade->student_id,
                'student' => $studentGrade->student,
                'competency_count' => count($studentGrade->studentCompetency),
                'avg' => round($studentGrade->studentCompetency->avg('score'), 0),
                'sum' => $studentGrade->studentCompetency->sum('score'),
                'metadata' => $studentGrade->studentCompetency,
                'description' => $description,
                'metadata' => $metadata,
            ]);
        };

        // Sort data by 'sum' in descending order
        $data = $data->sortByDesc('sum')->values();

        // Add ranking
        $data = $data->map(function ($item, $index) {
            $item['rank'] = $index + 1; // Rank starts from 1
            return $item;
        });

        // kembalikan data sort by id
        $data = $data->sortByDesc('student_id')->values();

        // dd($data->toArray());

        $this->students = $data;

        $this->form->fill([
            'leger' => $data,
            'time_signature' => now(),
        ]);
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
                        Hidden::make('leger'),
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
        // dd($this->form->getState());
        $data = $this->form->getState()['leger'];

        // insert data ke table leger
        foreach ($data as $key) {
            ModelsLeger::updateOrCreate([
                'academic_year_id' => $key['academic_year_id'],
                'student_id' => $key['student_id'],
                'teacher_subject_id' => $key['teacher_subject_id'],
            ], [
                'score' => $key['avg'],
                'description' => $key['description'],
                'metadata' => $key['metadata'],
            ]);
        }
    }

    public function getDescription($data)
    {
        $string = '';

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
        $notPassed = 'Ananda perlu peningkatan lagi pada materi materi: ';
        $countPassed = 0;
        $countNotPassed = 0;

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
