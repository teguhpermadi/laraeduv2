<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectCoordinatorResource\Pages;
use App\Filament\Resources\ProjectCoordinatorResource\RelationManagers;
use App\Models\Grade;
use App\Models\ProjectCoordinator;
use App\Models\Teacher;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProjectCoordinatorResource extends Resource
{
    protected static ?string $model = ProjectCoordinator::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Project';

    // get model label
    public static function getModelLabel(): string
    {
        return __('project-coordinator.title');
    }   

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('academic_year_id')
                ->default(session()->get('academic_year_id')),
                Select::make('teacher_id')
                    ->label(__('project-coordinator.teacher'))
                    ->options(Teacher::pluck('name', 'id'))
                    ->required(),
                Select::make('grade_id')
                    ->label(__('project-coordinator.grade'))
                    ->options(Grade::pluck('name', 'id'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('teacher.name')
                    ->label(__('project-coordinator.teacher')),
                TextColumn::make('grade.name')
                    ->label(__('project-coordinator.grade')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (ProjectCoordinator $record) {
                        $user = Teacher::find($record->teacher_id)->userable->user;
                        $user->removeRole('project');
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageProjectCoordinators::route('/'),
        ];
    }
}
