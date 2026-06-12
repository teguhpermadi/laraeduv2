<?php

namespace App\Filament\Resources\StudentCompetencyResource\Pages;

use App\Filament\Resources\StudentCompetencyResource;
use App\Models\AcademicYear;
use App\Models\TeacherSubject;
use App\Imports\RdmImport;
use App\Imports\RdmSumatifImport;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class UploadRdmtoStudentCompetencyPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = StudentCompetencyResource::class;

    protected static string $view = 'filament.resources.student-competency-resource.pages.upload-rdmto-student-competency-page';

    protected static ?string $title = 'Import Data Nilai dari RDM';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'academic_year_id' => session()->get('academic_year_id'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Select::make('academic_year_id')
                    ->label('Tahun Akademik')
                    ->options(function () {
                        return AcademicYear::all()->mapWithKeys(function ($item) {
                            return [$item->id => "{$item->year} - Sem. {$item->semester}"];
                        });
                    })
                    ->default(fn() => session()->get('academic_year_id'))
                    ->live()
                    ->required(),

                Repeater::make('uploads')
                    ->label('Upload File RDM')
                    ->schema([
                        FileUpload::make('file')
                            ->label('File Excel')
                            ->disk('public')
                            ->directory('uploads')
                            ->acceptedFileTypes([
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'application/vnd.ms-excel',
                                'application/x-excel',
                            ])
                            ->getUploadedFileNameForStorageUsing(
                                function (TemporaryUploadedFile $file) {
                                    return 'rdm_upload_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                                }
                            )
                            ->required(),

                        Select::make('teacher_subject_id')
                            ->label('Guru & Mata Pelajaran')
                            ->options(function (callable $get) {
                                $academicYearId = $get('../../academic_year_id');
                                if (!$academicYearId) {
                                    return [];
                                }
                                return TeacherSubject::withoutGlobalScope(\App\Models\Scopes\AcademicYearScope::class)
                                    ->where('academic_year_id', $academicYearId)
                                    ->with(['teacher', 'subject', 'grade'])
                                    ->get()
                                    ->mapWithKeys(function ($item) {
                                        $teacherName = $item->teacher?->name ?? '-';
                                        $subjectName = $item->subject?->name ?? '-';
                                        $gradeName = $item->grade?->name ?? '-';
                                        return [$item->id => "{$teacherName} - Kelas {$gradeName} - {$subjectName}"];
                                    });
                            })
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('import_type')
                            ->label('Tipe Import / Form RDM')
                            ->options([
                                'rdm' => 'RDM Import (Nilai Sumatif Harian)',
                                'rdm_sumatif' => 'RDM Sumatif Import (Nilai SAS/PAS)',
                            ])
                            ->required(),
                    ])
                    ->columns(3)
                    ->createItemButtonLabel('Tambah Upload File')
                    ->defaultItems(1)
            ]);
    }

    public function submit(): void
    {
        $formData = $this->form->getState();
        $uploads = $formData['uploads'] ?? [];

        if (empty($uploads)) {
            Notification::make()
                ->title('Peringatan')
                ->body('Tidak ada file yang diunggah.')
                ->warning()
                ->send();
            return;
        }

        $successCount = 0;
        $errors = [];

        foreach ($uploads as $index => $upload) {
            $file = $upload['file'];
            $teacherSubjectId = $upload['teacher_subject_id'];
            $importType = $upload['import_type'];

            try {
                if ($importType === 'rdm') {
                    $academicYearId = session()->get('academic_year_id');
                    $import = new RdmImport($teacherSubjectId, $academicYearId);
                    $import->processImport($file);
                    $successCount++;
                } elseif ($importType === 'rdm_sumatif') {
                    $import = new RdmSumatifImport($teacherSubjectId);
                    $result = $import->processImport($file);

                    if (!empty($result['error'])) {
                        $errors[] = "Baris ke-" . ($index + 1) . ": " . $result['error'];
                    } else {
                        $successCount++;
                    }
                }
            } catch (\Exception $e) {
                $errors[] = "Baris ke-" . ($index + 1) . ": Gagal memproses file. " . $e->getMessage();
            }
        }

        if ($successCount > 0) {
            Notification::make()
                ->title('Berhasil')
                ->body("Berhasil mengimpor {$successCount} file.")
                ->success()
                ->send();
        }

        if (!empty($errors)) {
            Notification::make()
                ->title('Beberapa file gagal diimpor')
                ->body(implode('<br>', $errors))
                ->danger()
                ->persistent()
                ->send();
        }

        if (empty($errors)) {
            $this->redirect(StudentCompetencyResource::getUrl('index'));
        }
    }

    public function getCancelUrl(): string
    {
        return StudentCompetencyResource::getUrl('index');
    }
}
