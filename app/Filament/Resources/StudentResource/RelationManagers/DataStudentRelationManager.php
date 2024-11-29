<?php

namespace App\Filament\Resources\StudentResource\RelationManagers;

use App\Models\DataStudent;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentModalRelationManagers\Concerns\CanBeEmbeddedInModals;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DataStudentRelationManager extends RelationManager
{
    use CanBeEmbeddedInModals;

    protected static string $relationship = 'dataStudent';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('data diri')
                    ->schema([
                        Forms\Components\TextInput::make('religion')
                            ->label(__('student.religion'))
                            ->required(),
                        Forms\Components\TextInput::make('previous_school')
                            ->label(__('student.previous_school')),
                    ])
                    ->columns(2),
                Section::make('alamat')
                    ->schema([
                        Forms\Components\TextInput::make('student_address')
                            ->label(__('student.address'))
                            ->required(),
                        Forms\Components\TextInput::make('student_province')
                            ->label(__('student.province'))
                            ->required(),
                        Forms\Components\TextInput::make('student_city')
                            ->label(__('student.city'))
                            ->required(),
                        Forms\Components\TextInput::make('student_district')
                            ->label(__('student.district'))
                            ->required(),
                        Forms\Components\TextInput::make('student_village')
                            ->label(__('student.village'))
                            ->required(),
                    ])
                    ->columns(2),
                Section::make('data ayah')
                    ->schema([
                        Forms\Components\TextInput::make('father_name')
                            ->label(__('student.father_name'))
                            ->required(),
                        Forms\Components\TextInput::make('father_education')
                            ->label(__('student.father_education')),
                        Forms\Components\TextInput::make('father_occupation')
                            ->label(__('student.father_occupation')),
                        Forms\Components\TextInput::make('father_phone')
                            ->label(__('student.father_phone')),
                    ])
                    ->columns(2),
                Section::make('data ibu')
                    ->schema([
                        Forms\Components\TextInput::make('mother_name')
                            ->label(__('student.mother_name'))
                            ->required(),
                        Forms\Components\TextInput::make('mother_education')
                            ->label(__('student.mother_education')),
                        Forms\Components\TextInput::make('mother_occupation')
                            ->label(__('student.mother_occupation')),
                        Forms\Components\TextInput::make('mother_phone')
                            ->label(__('student.mother_phone')),
                    ])
                    ->columns(2),
                Section::make('data wali')
                    ->schema([
                        Forms\Components\TextInput::make('guardian_name')
                            ->label(__('student.guardian_name')),
                        Forms\Components\TextInput::make('guardian_education')
                            ->label(__('student.guardian_education')),
                        Forms\Components\TextInput::make('guardian_occupation')
                            ->label(__('student.guardian_occupation')),
                        Forms\Components\TextInput::make('guardian_phone')
                            ->label(__('student.guardian_phone')),
                        Forms\Components\TextInput::make('guardian_address')
                            ->label(__('student.guardian_address')),
                    ])
                    ->columns(2),
                Section::make('data orang tua')
                    ->schema([
                        Forms\Components\TextInput::make('parent_address')
                            ->label(__('student.parent_address')),
                        Forms\Components\TextInput::make('parent_province')
                            ->label(__('student.parent_province')),
                        Forms\Components\TextInput::make('parent_city')
                            ->label(__('student.parent_city')),
                        Forms\Components\TextInput::make('parent_district')
                            ->label(__('student.parent_district')),
                        Forms\Components\TextInput::make('parent_village')
                            ->label(__('student.parent_village')),
                    ])
                    ->columns(2),
                Section::make('data lainnya')
                    ->schema([
                        Forms\Components\DatePicker::make('date_received')
                            ->label(__('student.date_received')),
                        Forms\Components\TextInput::make('grade_received')
                            ->label(__('student.grade_received')),
                    ])
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('student.name')
            ->columns([
                Tables\Columns\TextColumn::make('father_name')
                    ->label(__('student.father_name')),
                Tables\Columns\TextColumn::make('mother_name')
                    ->label(__('student.mother_name')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->slideOver()
                    ->closeModalByClickingAway(false)
                    ->using(function (array $data):Model {
                        return DataStudent::updateOrCreate(
                            ['student_id' => $this->ownerRecord->id],
                            $data
                        );
                    }),
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
            ]);
    }
}
