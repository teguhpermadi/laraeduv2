<?php

namespace App\Filament\Resources\GradeResource\RelationManagers;

use App\Enums\CurriculumEnum;
use App\Models\Teacher;
use App\Models\TeacherGrade;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentModalRelationManagers\Concerns\CanBeEmbeddedInModals;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TeacherGradeRelationManager extends RelationManager
{
    use CanBeEmbeddedInModals;  
    
    protected static string $relationship = 'teacherGrade';

    public static function getModelLabel(): string
    {
        return __('teacherGrade.teachergrade');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('academic_year_id')
                    ->default(session()->get('academic_year_id')),
                Select::make('teacher_id')
                    ->label(__('teacherGrade.teacher_id'))
                    ->options(Teacher::pluck('name', 'id'))
                    ->required(),
                Select::make('curriculum')
                    ->label(__('teacherGrade.curriculum'))
                    ->options(CurriculumEnum::class)
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('teacher_id')
            ->columns([
                Tables\Columns\TextColumn::make('teacher.name')
                    ->label(__('teacherGrade.teacher_id')),
                Tables\Columns\TextColumn::make('curriculum')
                    ->label(__('teacherGrade.curriculum')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->slideOver()
                    ->closeModalByClickingAway(false)
                    ->createAnother(false),
                    // ->using(function($data){
                    //     $data += ['grade_id' => $this->getOwnerRecord()->getKey()];

                    //     return TeacherGrade::updateOrCreate([
                    //         'academic_year_id' => $data['academic_year_id'],
                    //         'grade_id' => $data['grade_id'],
                    //     ], $data);
                    // }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->slideOver()
                    ->closeModalByClickingAway(false),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
