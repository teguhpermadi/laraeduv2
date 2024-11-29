<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExtracurricularResource\Pages;
use App\Filament\Resources\ExtracurricularResource\RelationManagers;
use App\Filament\Resources\ExtracurricularResource\RelationManagers\StudentExtracurricularRelationManager;
use App\Filament\Resources\ExtracurricularResource\RelationManagers\TeacherExtracurricularRelationManager;
use App\Models\Extracurricular;
use App\Models\Student;
use App\Models\StudentExtracurricular;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentModalRelationManagers\Actions\RelationManagerAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExtracurricularResource extends Resource
{
    protected static ?string $model = Extracurricular::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Pengaturan';

    public static function getNavigationLabel(): string
    {
        return __('extracurricular.extracurricular');
    }

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // buakan form
                Forms\Components\TextInput::make('name')
                    ->label(__('extracurricular.name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_required')
                    ->label(__('extracurricular.is_required'))
                    ->default(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('extracurricular.name')),
                Tables\Columns\TextColumn::make('student_extracurricular_count')
                    ->label('Jumlah Siswa')
                    ->counts('studentExtracurricular'),
                Tables\Columns\ToggleColumn::make('is_required')
                    ->label(__('extracurricular.is_required'))
                    ->afterStateUpdated(function (Builder $query, $state, Extracurricular $record) {
                        // dd($query);
                        if ($state) {
                            $students = Student::pluck('id');
                            foreach ($students as $student) {
                                StudentExtracurricular::updateOrCreate([
                                    'academic_year_id' => session('academic_year_id'),
                                    'student_id' => $student,
                                    'extracurricular_id' => $record->id,
                                ]);
                            }

                            // notifikasi
                            Notification::make()
                                ->title('Berhasil')
                                ->body('Semua siswa berhasil ditambahkan ke ekstrakurikuler ini.')
                                ->success()
                                ->send();
                        } else {
                            // delete semua data student extracurricular
                            StudentExtracurricular::where('extracurricular_id', $record->id)->delete();

                            // notifikasi
                            Notification::make()
                                ->title('Berhasil')
                                ->body('Semua siswa berhasil dihapus dari ekstrakurikuler ini.')
                                ->success()
                                ->send();
                        }
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                RelationManagerAction::make('student-extracurricular-relation-manager')
                    ->label('Peserta')
                    ->button()
                    ->slideOver()
                    ->relationManager(StudentExtracurricularRelationManager::make()),
                RelationManagerAction::make('teacher-extracurricular-relation-manager')
                    ->label('Guru')
                    ->button()
                    ->slideOver()
                    ->relationManager(TeacherExtracurricularRelationManager::make()),
                Tables\Actions\EditAction::make(),
                // Tables\Actions\Action::make('Leger')
                //     ->url(fn (Extracurricular $record) => route('leger-extracurricular', $record->id))
                //     ->icon('heroicon-o-document-text'),
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
            StudentExtracurricularRelationManager::class,
            TeacherExtracurricularRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExtracurriculars::route('/'),
            'create' => Pages\CreateExtracurricular::route('/create'),
            'edit' => Pages\EditExtracurricular::route('/{record}/edit'),
        ];
    }
}
