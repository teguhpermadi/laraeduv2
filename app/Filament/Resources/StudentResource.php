<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Filament\Resources\StudentResource\RelationManagers\DataStudentRelationManager;
use App\Filament\Resources\StudentResource\RelationManagers\StudentGradeRelationManager;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;
use Guava\FilamentModalRelationManagers\Actions\Table\RelationManagerAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Pengaturan';

    public static function getModelLabel(): string
    {
        return __('student.student');
    }
    protected static ?int $navigationSort = 2;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nisn')
                    ->label(__('student.nisn'))
                    ->required(),
                TextInput::make('nis')
                    ->label(__('student.nis'))
                    ->required(),
                TextInput::make('name')
                    ->label(__('student.name'))
                    ->required(),
                TextInput::make('nick_name')
                    ->label(__('student.nick_name')),
                Select::make('gender')
                    ->label(__('student.gender'))
                    ->options([
                        'laki-laki' => 'laki-laki',
                        'perempuan' => 'perempuan'
                    ])
                    ->required(),
                TextInput::make('city_born')
                    ->label(__('student.city_born')),
                DatePicker::make('birthday')
                    ->label(__('student.birthday')),
                FileUpload::make('photo')
                    ->label(__('student.photo'))
                    ->directory('photo_students')
                    ->image()
                    ->avatar()
                    ->imageEditor()
                    ->circleCropper()
                    ->optimize('jpg')
                    ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file, $record) {
                        return $record->nisn . '.' . $file->getClientOriginalExtension();
                    }),
                Toggle::make('active')
                    ->label(__('student.active')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')
                    ->circular()
                    ->size(100),
                TextColumn::make('name')
                    ->label(__('student.name'))
                    ->searchable(),
                TextColumn::make('gender')
                    ->label(__('student.gender')),
                TextInputColumn::make('nis')
                    ->label(__('student.nis')),
                TextInputColumn::make('nisn')
                    ->label(__('student.nisn'))
                    ->searchable(),
                TextInputColumn::make('birthday')
                    ->label(__('student.birthday'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextInputColumn::make('dataStudent.student_address')
                    ->label(__('student.student_address'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextInputColumn::make('dataStudent.student_province')
                    ->label(__('student.student_province'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextInputColumn::make('dataStudent.student_city')
                    ->label(__('student.student_city'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextInputColumn::make('dataStudent.student_district')
                    ->label(__('student.student_district'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextInputColumn::make('dataStudent.student_village')
                    ->label(__('student.student_village'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextInputColumn::make('dataStudent.religion')
                    ->label(__('student.religion'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextInputColumn::make('dataStudent.previous_school')
                    ->label(__('student.previous_school'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextInputColumn::make('dataStudent.father_name')
                    ->label(__('student.father_name'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextInputColumn::make('dataStudent.father_education')
                    ->label(__('student.father_education'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextInputColumn::make('dataStudent.father_occupation')
                    ->label(__('student.father_occupation'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextInputColumn::make('dataStudent.father_phone')
                    ->label(__('student.father_phone'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextInputColumn::make('dataStudent.mother_name')
                    ->label(__('student.mother_name'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextInputColumn::make('dataStudent.mother_education')
                    ->label(__('student.mother_education'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextInputColumn::make('dataStudent.mother_occupation')
                    ->label(__('student.mother_occupation'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextInputColumn::make('dataStudent.mother_phone')
                    ->label(__('student.mother_phone'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextInputColumn::make('dataStudent.guardian_name')
                    ->label(__('student.guardian_name'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextInputColumn::make('dataStudent.guardian_education')
                    ->label(__('student.guardian_education'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextInputColumn::make('dataStudent.guardian_occupation')
                    ->label(__('student.guardian_occupation'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextInputColumn::make('dataStudent.guardian_phone')
                    ->label(__('student.guardian_phone'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextInputColumn::make('dataStudent.guardian_address')
                    ->label(__('student.guardian_address'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextInputColumn::make('dataStudent.guardian_village')
                    ->label(__('student.guardian_village'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextInputColumn::make('dataStudent.parent_address')
                    ->label(__('student.parent_address'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextInputColumn::make('dataStudent.parent_province')
                    ->label(__('student.parent_province'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextInputColumn::make('dataStudent.parent_city')
                    ->label(__('student.parent_city'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextInputColumn::make('dataStudent.parent_district')
                    ->label(__('student.parent_district'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextInputColumn::make('dataStudent.parent_village')
                    ->label(__('student.parent_village'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextInputColumn::make('dataStudent.date_received')
                    ->label(__('student.date_received'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextInputColumn::make('dataStudent.grade_received')
                    ->label(__('student.grade_received'))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                RelationManagerAction::make('data-student-relation-manager')
                    ->label('data')
                    ->button()
                    ->slideOver()
                    ->relationManager(DataStudentRelationManager::class),
                RelationManagerAction::make('student-grade-relation-manager')
                    ->label('kelas')
                    ->button()
                    ->slideOver()
                    ->closeModalByClickingAway(false)
                    ->closeModalByClickingAway(false)
                    ->relationManager(StudentGradeRelationManager::class),
                Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            StudentGradeRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
