<?php

namespace App\Filament\Resources\QuranGradeResource\RelationManagers;

use App\Models\Teacher;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Guava\FilamentModalRelationManagers\Concerns\CanBeEmbeddedInModals;

class TeacherQuranGradeRelationManager extends RelationManager
{
    use CanBeEmbeddedInModals;
    
    protected static string $relationship = 'teacherQuranGrade';

    // title
    protected static ?string $title = 'Guru Mengaji';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('academic_year_id')
                    ->default(session('academic_year_id')),
                Select::make('teacher_id')
                    ->label(__('quran-grade.fields.teacher.label'))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->options(
                        // teacher yang memiliki teacher_quran
                        Teacher::query()
                            ->whereHas('teacherQuran', function (Builder $query) {
                                $query->where('academic_year_id', session('academic_year_id'));
                            })
                            ->pluck('name', 'id')
                    ),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('teacher.name')
            ->columns([
                Tables\Columns\TextColumn::make('teacher.name')
                    ->label(__('quran-grade.fields.teacher.label')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                ->closeModalByClickingAway(false)
                    ->slideOver(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->closeModalByClickingAway(false)
                    ->slideOver(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->paginated(false);
    }
}
