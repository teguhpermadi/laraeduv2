<?php

namespace App\Filament\Resources;

use App\Enums\SemesterEnum;
use App\Filament\Resources\AcademicYearResource\Pages;
use App\Filament\Resources\AcademicYearResource\RelationManagers;
use App\Jobs\CopyProjectCoordinatorJob;
use App\Jobs\CopyStudentExtracurricularJob;
use App\Jobs\CopyStudentGradeJob;
use App\Jobs\CopyTeacherExtracurricularJob;
use App\Jobs\CopyTeacherGradeJob;
use App\Jobs\CopyTeacherSubjectsJob;
use App\Models\AcademicYear;
use App\Models\StudentGrade;
use App\Models\Teacher;
use App\Models\TeacherGrade;
use App\Models\TeacherSubject;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AcademicYearResource extends Resource
{
    protected static ?string $model = AcademicYear::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Pengaturan';

    public static function getNavigationLabel(): string
    {
        return __('academic-year.list.academic_year');
    }

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('year')
                    ->mask('9999/9999')
                    ->label(__('academic-year.create.year'))
                    ->required(),
                Select::make('semester')
                    ->label(__('academic-year.create.semester'))
                    ->options(SemesterEnum::class)
                    ->required(),
                Select::make('teacher_id')
                    ->label(__('academic-year.create.teacher_id'))
                    ->options(Teacher::all()->pluck('name', 'id')),
                DatePicker::make('date_report_half')
                    ->label('Tanggal rapor tengah semester'),
                DatePicker::make('date_report')
                    ->required()
                    ->label('Tanggal rapor akhir semester'),
                DatePicker::make('date_graduation')
                    ->visible(function (Get $get) {
                        return $get('semester') == SemesterEnum::GENAP->value;
                    })
                    ->label('Tanggal lulus'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('year')
                    ->label(__('academic-year.list.year')),
                TextColumn::make('semester')
                    ->label(__('academic-year.list.semester')),
                TextColumn::make('teacher.name')
                    ->label(__('academic-year.list.teacher_id')),
                TextColumn::make('date_report_half')
                    ->label('Tanggal rapor tengah semester')
                    ->date(),
                TextColumn::make('date_report')
                    ->label('Tanggal rapor akhir semester')
                    ->date(),
                TextColumn::make('date_graduation')
                    ->label('Tanggal lulus')
                    ->date(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
                Action::make('copy')
                    ->label('Copy')
                    ->visible(fn(AcademicYear $record) => $record->semester == SemesterEnum::GENAP->value) // tampilkan tombol copy hanya pada semester genap
                    ->accessSelectedRecords()
                    ->button()
                    ->form([
                        Select::make('academic_year_id')
                            ->label('Tahun Akademik yang ingin di copy')
                            ->options(function (AcademicYear $record) {
                                return AcademicYear::query()
                                    ->where('id', '!=', $record->id)
                                    ->get()
                                    ->map(function ($item) {
                                        return [
                                            'id' => $item->id,
                                            'year' => $item->year . ' - ' . $item->semester,
                                        ];
                                    })
                                    ->pluck('year', 'id');
                            })
                            ->required(),
                        CheckboxList::make('targets')
                            ->label('Data yang akan di copy')
                            ->options([
                                'studentGrade' => 'Data Kelas Siswa',
                                'teacherGrade' => 'Data Wali Kelas',
                                'teacherSubject' => 'Data Guru dan Mata Pelajaran',
                                'teacherExtracurricular' => 'Data Guru Ekstrakurikuler',
                                'studentExtracurricular' => 'Data Siswa Ekstrakurikuler',
                                'projectCoordinator' => 'Data Koordinator Proyek',
                            ])
                            ->required(),
                    ])
                    ->action(function (array $data, AcademicYear $record) {
                        $sourceAcademicYearId = $data['academic_year_id'];
                        $targetAcademicYearId = $record->id;
                        $selectedTargets = $data['targets'];

                        // Mapping data yang akan di-copy
                        // ... existing code ...

                        $copyMethods = [
                            'teacherSubject' => function ($sourceId, $targetId) {
                                CopyTeacherSubjectsJob::dispatch($sourceId, $targetId);
                            },
                            'studentGrade' => function ($sourceId, $targetId) {
                                CopyStudentGradeJob::dispatch($sourceId, $targetId);
                            },
                            'teacherGrade' => function ($sourceId, $targetId) {
                                self::copyTeacherGrades($sourceId, $targetId);
                            },
                            'teacherExtracurricular' => function ($sourceId, $targetId) {
                                self::copyTeacherExtracurriculars($sourceId, $targetId);
                            },
                            'studentExtracurricular' => function ($sourceId, $targetId) {
                                self::copyStudentExtracurriculars($sourceId, $targetId);
                            },
                            'projectCoordinator' => function ($sourceId, $targetId) {
                                self::copyProjectCoordinators($sourceId, $targetId);
                            },
                        ];

                        // Eksekusi copy data berdasarkan target yang dipilih
                        foreach ($selectedTargets as $target) {
                            if (isset($copyMethods[$target])) {
                                $copyMethods[$target]($sourceAcademicYearId, $targetAcademicYearId);
                            }
                        }

                        // notifikasi kalau job dilakukan di latar belakang
                        Notification::make()
                            ->title('Copy data ini dilakukan di latar belakang')
                            ->body('Silahkan cek log untuk melihat progress copy data')
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAcademicYears::route('/'),
            'create' => Pages\CreateAcademicYear::route('/create'),
            'edit' => Pages\EditAcademicYear::route('/{record}/edit'),
        ];
    }

    /**
     * Copy data nilai siswa dari satu tahun akademik ke tahun akademik lain
     *
     * @param int $sourceAcademicYearId
     * @param int $targetAcademicYearId
     * @return void
     */
    private static function copyStudentGrades($sourceAcademicYearId, $targetAcademicYearId)
    {
        // Dispatch job untuk copy student grades
        CopyStudentGradeJob::dispatch($sourceAcademicYearId, $targetAcademicYearId);
    }

    /**
     * Copy data wali kelas dari satu tahun akademik ke tahun akademik lain
     *
     * @param int $sourceAcademicYearId
     * @param int $targetAcademicYearId
     * @return void
     */
    private static function copyTeacherGrades($sourceAcademicYearId, $targetAcademicYearId)
    {
        CopyTeacherGradeJob::dispatch($sourceAcademicYearId, $targetAcademicYearId);
    }

    private static function copyTeacherExtracurriculars($sourceAcademicYearId, $targetAcademicYearId)
    {
        CopyTeacherExtracurricularJob::dispatch($sourceAcademicYearId, $targetAcademicYearId);
    }

    private static function copyStudentExtracurriculars($sourceAcademicYearId, $targetAcademicYearId)
    {
        CopyStudentExtracurricularJob::dispatch($sourceAcademicYearId, $targetAcademicYearId);
    }

    private static function copyProjectCoordinators($sourceAcademicYearId, $targetAcademicYearId)
    {
        CopyProjectCoordinatorJob::dispatch($sourceAcademicYearId, $targetAcademicYearId);
    }
}
