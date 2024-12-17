<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Enums\LinkertScaleEnum;
use App\Filament\Resources\ProjectResource;
use App\Imports\StudentProjectImport;
use App\Models\Project;
use App\Models\ProjectTarget;
use App\Models\StudentGrade;
use App\Models\StudentProject;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Filament\Actions\Action as HeaderAction;
use Filament\Forms\Components\FileUpload;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ProjectAssesment extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string $resource = ProjectResource::class;

    protected static string $view = 'filament.resources.project-resource.pages.project-assesment';

    public $options = [];
    public $projectTargetId = -1;
    public $record, $target_id;

    public ?array $data = [];

    public function mount($record)
    {
        $targets = ProjectTarget::where('project_id', $record)->get();
        foreach ($targets as $target) {
            $this->options[$target->id] = $target->target->description;
        }
        $this->record = $record;
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Capaian')
                    ->label(__('project.target'))
                    ->schema([
                        Radio::make('target_id')
                            ->label(__('project.target'))
                            ->options($this->options)
                            ->required()
                            ->live()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('projectTargetId', $state);
                            }),
                    ]),
            ]);
    }

    public function submit()
    {
        // dd($this->form->getState());
        $this->projectTargetId = $this->form->getState()['target_id'];
        // dd($this->projectTargetId);
    }

    // buatkan header action pada custom page ini
    protected function getHeaderActions(): array
    {
        return [
            HeaderAction::make('edit')
                ->label(__('project.edit'))
                ->url(fn () => route('filament.admin.resources.projects.edit', $this->record)),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(StudentProject::query())
            ->columns([
                TextColumn::make('student.name')
                    ->label(__('project.student')),
                SelectColumn::make('score')
                    ->label(__('project.score'))
                    ->options(LinkertScaleEnum::class)
            ])
            ->bulkActions([
                BulkAction::make('scoring')
                    ->label(__('project.scoring'))
                    ->slideOver()
                    ->closeModalByClickingAway(false)
                    ->modalWidth('sm')
                    ->form([
                        Select::make('score')
                            ->label(__('project.score'))
                            ->options(LinkertScaleEnum::class)
                    ])
                    ->action(function (Collection $records, $data) {
                        $records->each->update($data);
                    })
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query->where('project_target_id', $this->projectTargetId);
            })
            ->paginated(false)
            ->headerActions([
                Action::make('reset')
                    ->label(__('project.reset'))
                    ->action(fn () => $this->resetScore()),
                Action::make('download')
                    ->label(__('project.download'))
                    ->action(fn () => $this->download()),
                Action::make('upload')
                    ->label(__('project.upload'))
                    ->slideOver()
                    ->closeModalByClickingAway(false)
                    ->modalWidth('sm')
                    ->form([
                        FileUpload::make('file')
                            ->directory('uploads')
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/x-excel'])
                            ->getUploadedFileNameForStorageUsing(
                                function (TemporaryUploadedFile $file) {
                                    return 'nilai.' . $file->getClientOriginalExtension();
                                }
                            )
                            ->required()
                    ])
                    ->action(function (array $data) {
                        $studentProjects = Excel::toArray(new StudentProjectImport, storage_path('/app/public/' . $data['file']));
                        foreach ($studentProjects as $row) {
                            foreach ($row as $value) {
                                StudentProject::updateOrCreate([
                                    'academic_year_id' => $value['academic_year_id'],
                                    'student_id' => $value['student_id'],
                                    'project_target_id' => $value['project_target_id'],
                                ], [
                                    'score' => $value['score'],
                                ]);
                            }
                        }
                    })
                    ->closeModalByClickingAway(false),
            ]);
    }

    // reset student project agar score menjadi 0
    public function resetScore()
    {
        // ambil detil project berdasarkan id
        $project = Project::find($this->record);
        // dd($project->projectTarget);
        // cek terlebih dahulu student_id berdasarkan grade_id dari project
        $students = StudentGrade::where('grade_id', $project->grade_id)->get();
        // dd($students);

        foreach ($students as $student) {
            foreach ($project->projectTarget as $target) {

                // delete all student project berdasarkan academic_year_id, student_id, project_target_id
                StudentProject::where('academic_year_id', session('academic_year_id'))
                    ->where('project_target_id', $target->id)
                    ->delete();

                StudentProject::create([
                    'academic_year_id' => session('academic_year_id'),
                    'student_id' => $student->student_id,
                    'project_target_id' => $target->id,
                    'score' => 0,
                ]);
            }
        }
    }

    public function download()
    {
        $project = Project::find($this->record);
        $target = $project->projectTarget;
        $academic = $project->academic;
        $teacher = $project->teacher;
        $grade = $project->grade;
        $students = $grade->studentGrade;
        $countStudent = $students->count();

        // inisialisasi spreadsheet
        $spreadsheet = new Spreadsheet();
        $countSheet = 0;

        // buat sheet berdasarkan banyaknya project target
        foreach ($target as $target) {
            $spreadsheet->createSheet();
            $sheet = $spreadsheet->getSheet($countSheet);
            // $sheet->setTitle('Target id-' . $target->target->id);

            // identitas
            $identitas = [
                ['Identitas pelajaran'],
                [null],
                ['Nama Guru',': ' . $teacher->name],
                ['Project', ': ' . $project->name],
                ['Kelas', ': ' . $grade->name],
                ['Tahun Akademik', ': ' . $academic->year],
                ['Semester', ': ' . $academic->semester],
                ['Target', ': ' . $target->target->description],
            ];

            $sheet->fromArray($identitas);

            // kosongkan datanya
            $data = [];
            $data[] = [
                'NIS',
                'Nama Siswa',
                'score',
                'academic_year_id',
                'student_id',
                'project_target_id',
            ];

            foreach($target->studentProject as $studentProject) {
                $data[] = [
                    $studentProject->student->nis,
                    $studentProject->student->name,
                    $studentProject->score,
                    $studentProject->academic_year_id,
                    $studentProject->student_id,
                    $studentProject->project_target_id,
                ];
            }

            $sheet->fromArray($data, null, 'A13', true);

            // pedoman penilaian
            $rubrik = [
                ['Pedoman Penilaian'],
                ['4', 'Amat Baik'],
                ['3', 'Baik'],
                ['2', 'Cukup'],
                ['1', 'Kurang'],
                ['0', 'Amat Kurang'],
            ];

            $sheet->fromArray($rubrik, null, 'G13', true);

            // berikan background kuning muda pada cell G13 sampai G18
            $sheet->getStyle('G13:G18')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFFF00');

            $countSheet++;

            $sheet->getColumnDimension('A')->setWidth(15);
            $sheet->getColumnDimension('B')->setWidth(30);

            // count student
            $rowStudent = 13 + $countStudent;         
            
            // bisa di edit
            $sheet->getStyle('C14:C' . $rowStudent)->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);
            
            // proteksi semua cell
            $sheet->getProtection()->setPassword('PhpSpreadsheet');
            $spreadsheet->getActiveSheet()->getProtection()->setSheet(true);

            // validasi tiap-tiap cell
            for ($i = 14; $i <= $rowStudent; $i++) {
                $validation = $sheet->getCell('C' . $i)->getDataValidation();
                $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_WHOLE);
                $validation->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_STOP);
                $validation->setAllowBlank(false);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setErrorTitle('Input error');
                $validation->setError('Number is not allowed!');
                $validation->setPromptTitle('Allowed input');
                $validation->setPrompt('Only numbers between 0 and 4 are allowed.');
                $validation->setFormula1(0);
                $validation->setFormula2(4);
            }

            // berikan warna putih pada font pada cell D sampai F
            $sheet->getStyle('D13:F' . $rowStudent)->getFont()->getColor()->setARGB('FFFFFFFF');

            // Set active cell to C14
            $sheet->setSelectedCell('C14');
        }

        // download file
        $writer = new Xlsx($spreadsheet);
        // Set sheet pertama sebagai sheet aktif
        $spreadsheet->setActiveSheetIndex(0);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx'); // <<< HERE
        // $filename = "studentCompetency-".$subject->code.".xlsx"; // <<< HERE
        $filename = "nilai " . $teacher->name . ' ' . $grade->name . ".xlsx"; // <<< HERE
        $file_path = storage_path('/app/public/downloads/' . $filename);
        $writer->save($file_path);
        return response()->download($file_path)->deleteFileAfterSend(true); // <<< HERE
    }
}
