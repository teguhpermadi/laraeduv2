<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentExtracurricularResource\Pages;
use App\Filament\Resources\StudentExtracurricularResource\RelationManagers;
use App\Enums\LinkertScaleEnum;
use App\Exports\StudentExtracurricularExport;
use App\Models\Extracurricular;
use App\Models\Student;
use App\Models\StudentExtracurricular;
use App\Models\TeacherExtracurricular;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class StudentExtracurricularResource extends Resource
{
    protected static ?string $model = StudentExtracurricular::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Pelajaran Ku';

    protected static ?string $modelLabel = 'Penilaian Ekstrakurikuler';

    protected static ?string $pluralModelLabel = 'Penilaian Ekstrakurikuler';

    protected static ?int $navigationSort = 9;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('academic_year_id')
                    ->default(session('academic_year_id')),
                Select::make('extracurricular_id')
                    ->label(__('extracurricular.extracurricular'))
                    ->options(Extracurricular::all()->pluck('name', 'id'))
                    ->live()
                    ->reactive(),
                Select::make('student_id')
                    ->label(__('extracurricular.student'))
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->options(
                        function (Get $get) {
                            // tampilkan semua student yang belum memiliki student_extracurricular
                            $students = Student::query()
                                ->whereDoesntHave('studentExtracurricular', function ($query) use ($get) {
                                    $query->where('extracurricular_id', $get('extracurricular_id'));
                                })
                                ->pluck('name', 'id');

                            return $students;
                        }
                    ),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('student.photo')
                    ->circular()
                    ->size(100),
                TextColumn::make('student.name')
                    ->label(__('extracurricular.student')),
                TextColumn::make('extracurricular.name')
                    ->label(__('extracurricular.extracurricular')),
                SelectColumn::make('score')
                    ->label(__('extracurricular.score'))
                    ->options(LinkertScaleEnum::class),
            ])
            ->filters([])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Action::make('Lihat foto')
                    ->slideOver()
                    ->modalWidth('sm')
                    ->modalHeading(fn(StudentExtracurricular $record) => $record->student->name)
                    ->modalContent(function (StudentExtracurricular $record) {
                        return view('student-photo-preview', compact('record'));
                    })
            ], position: ActionsPosition::BeforeColumns)
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    BulkAction::make('Export')
                        ->action(function (Collection $records) {
                            $data = StudentExtracurricular::whereIn('id', $records->pluck('id'))->get();
                            // Inisialisasi spreadsheet
                            $spreadsheet = new Spreadsheet();

                            // dapatkan sheet yang sudah ada
                            $sheet = $spreadsheet->getSheet(0);

                            // buat header
                            $sheet->setCellValue('A1', 'No');
                            // foto
                            $sheet->setCellValue('B1', 'Foto');
                            // nama siswa
                            $sheet->setCellValue('C1', 'Nama Siswa');
                            // kelas siswa
                            $sheet->setCellValue('D1', 'Kelas Siswa');
                            // nama ekstrakurikuler
                            $sheet->setCellValue('E1', 'Nama Ekstrakurikuler');
                            // nilai
                            $sheet->setCellValue('F1', 'Nilai');

                            // tampilkan datanya
                            $row = 2;
                            foreach ($data as $item) {
                                $sheet->setCellValue('A' . $row, $row - 1);
                                // buat agar menjadi rata tengah
                                $sheet->getCell('A' . $row)->getStyle()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                                
                                // $sheet->setCellValue('B' . $row, $item->student->photo);
                                $drawing = new Drawing();
                                $drawing->setName($item->student->name);
                                $drawing->setDescription('Foto Siswa');

                                // jika foto tidak ada, maka tidak akan ditampilkan dan akan diisi dengan placeholder
                                if ($item->student->photo) {
                                    $drawing->setPath(asset('storage/' . $item->student->photo)); /* put your path and image here */
                                } else {
                                    $drawing->setPath('https://pic.pnnet.dev/300x400'); /* put your path and image here */
                                }

                                $drawing->setCoordinates('B' . $row);
                                // ubah ukuran foto menjadi 113x150
                                $drawing->setHeight(113);
                                $drawing->setWidth(150);
                                $drawing->setOffsetX(10);
                                $drawing->setOffsetY(10);
                                $drawing->setRotation(0);
                                $drawing->setWorksheet($spreadsheet->getActiveSheet());
                                // ubah row height menjadi 120
                                $sheet->getRowDimension($row)->setRowHeight(170);
                                $sheet->getColumnDimension('B')->setWidth(30);

                                $sheet->setCellValue('C' . $row, $item->student->name);
                                $sheet->setCellValue('D' . $row, $item->student->studentGradeFirst->grade->name);
                                $sheet->setCellValue('E' . $row, $item->extracurricular->name);
                                $sheet->setCellValue('F' . $row, $item->score);
                                
                                // buat agar menjadi rata tengah
                                $sheet->getCell('A' . $row)->getStyle()->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
                                $sheet->getCell('B' . $row)->getStyle()->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
                                $sheet->getCell('C' . $row)->getStyle()->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
                                $sheet->getCell('D' . $row)->getStyle()->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
                                $sheet->getCell('E' . $row)->getStyle()->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);
                                $sheet->getCell('F' . $row)->getStyle()->getAlignment()->setHorizontal(Alignment::VERTICAL_CENTER);

                                // buat kolom C menjadi width 30
                                $sheet->getColumnDimension('C')->setWidth(50);
                                // buat kolom C agar menjadi text wrap
                                $sheet->getCell('C' . $row)->getStyle()->getAlignment()->setWrapText(true);
                                
                                // buat kolom D menjadi width 20
                                $sheet->getColumnDimension('D')->setWidth(20);
                                $sheet->getColumnDimension('E')->setWidth(20);
                                
                                // berikan border pada cell
                                $sheet->getCell('A' . $row)->getStyle()->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
                                $sheet->getCell('B' . $row)->getStyle()->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
                                $sheet->getCell('C' . $row)->getStyle()->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
                                $sheet->getCell('D' . $row)->getStyle()->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
                                $sheet->getCell('E' . $row)->getStyle()->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
                                $sheet->getCell('F' . $row)->getStyle()->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN);
                                $row++;
                            }

                            // buat file excel
                            $writer = new Xlsx($spreadsheet);
                            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx'); // <<< HERE
                            // $filename = "studentCompetency-".$subject->code.".xlsx"; // <<< HERE
                            $filename = "nilai ekstrakurikuler.xlsx"; // <<< HERE
                            $file_path = storage_path('/app/public/downloads/' . $filename);
                            $writer->save($file_path);
                            return response()->download($file_path)->deleteFileAfterSend(true);
                        }),
                ]),
            ])
            ->recordUrl(null);
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
            'index' => Pages\ListStudentExtracurriculars::route('/'),
            'create' => Pages\CreateStudentExtracurricular::route('/create'),
            'edit' => Pages\EditStudentExtracurricular::route('/{record}/edit'),
        ];
    }
}
