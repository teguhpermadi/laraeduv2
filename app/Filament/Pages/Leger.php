<?php

namespace App\Filament\Pages;

use App\Models\TeacherSubject;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as SectionInfoList;
use Filament\Pages\Page;

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

    public $teacherSubject, $students, $time_signature, $preview, $student, $agree, $leger;

    public function mount($id): void
    {
        $competency = TeacherSubject::with('subject')->withCount('competency')->find($id);
        $this->teacherSubject = $competency;

        $competency_id = $competency->competency->pluck('id');

        $teacherSubject = TeacherSubject::with(['studentGrade.studentCompetency' => function ($query) use ($competency_id) {
            $query->whereIn('competency_id', $competency_id);
        }])->find($id);

        $data = collect();
        
        foreach ($teacherSubject->studentGrade as $studentGrade) {

            $data[$studentGrade->student_id] = collect([
                'academic_year_id' => $competency->academic_year_id,
                'grade_id' => $competency->grade_id,
                'teacher_id' => $competency->teacher_id,
                'subject_id' => $competency->subject_id,
                'student_id' => $studentGrade->student_id,
                'student' => $studentGrade->student,
                'competency_count' => count($studentGrade->studentCompetency),
                'avg' => $studentGrade->studentCompetency->avg('score'),
                'sum' => $studentGrade->studentCompetency->sum('score'),
                'metadata' => $studentGrade->studentCompetency,
            ]);
        };

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
                Section::make('preview')
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
        dd($this->form->getState());
    }
}
