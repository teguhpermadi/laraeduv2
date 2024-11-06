<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttitudeResource\Pages;
use App\Filament\Resources\AttitudeResource\RelationManagers;
use App\Models\AcademicYear;
use App\Models\Attitude;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttitudeResource extends Resource
{
    protected static ?string $model = Attitude::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return __('attitude.attitude');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('academic_year_id')->default(session()->get('academic_year_id')),
                Hidden::make('grade_id'),
                Select::make('student_id')
                    ->reactive()
                    ->required()
                    ->options(Student::myStudentGrade()->pluck('name', 'id'))
                    ->afterStateUpdated(function(callable $set, callable $get, $state){
                        $grade_id = Student::find($state)->studentGrade->first()->grade_id;
                        $set('grade_id', $grade_id);
                    }),
                Select::make('attitude_religius')
                    ->label(__('attitude.attitude_religius'))
                    ->options([
                        'amat baik' => 'amat baik',
                        'baik' => 'baik',
                        'cukup baik' => 'cukup baik',
                        'kurang baik' => 'kurang baik',
                    ])
                    ->required(),
                Select::make('attitude_social')
                    ->label(__('attitude.attitude_social'))
                    ->options([
                        'amat baik' => 'amat baik',
                        'baik' => 'baik',
                        'cukup baik' => 'cukup baik',
                        'kurang baik' => 'kurang baik',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.name')
                    ->label(__('student.name')),
                SelectColumn::make('attitude_religius')
                    ->label(__('attitude.attitude_religius'))
                    ->options([
                    'amat baik' => 'amat baik',
                    'baik' => 'baik',
                    'cukup baik' => 'cukup baik',
                    'kurang baik' => 'kurang baik',
                ]),
                SelectColumn::make('attitude_social')
                    ->label(__('attitude.attitude_social'))
                    ->options([
                        'amat baik' => 'amat baik',
                        'baik' => 'baik',
                        'cukup baik' => 'cukup baik',
                        'kurang baik' => 'kurang baik',
                ]),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                    BulkAction::make('assesment')
                    ->form([
                        Select::make('attitude_religius')
                            ->label(__('attitude.attitude_religius'))
                            ->options([
                                'amat baik' => 'amat baik',
                                'baik' => 'baik',
                                'cukup baik' => 'cukup baik',
                                'kurang baik' => 'kurang baik',
                            ])
                            ->required(),
                        Select::make('attitude_social')
                            ->label(__('attitude.attitude_social'))
                            ->options([
                                'amat baik' => 'amat baik',
                                'baik' => 'baik',
                                'cukup baik' => 'cukup baik',
                                'kurang baik' => 'kurang baik',
                            ])
                            ->required(),
                    ])
                    ->action(function (Collection $records, $data) {
                        $dataUpdate = [
                            'attitude_religius' => $data['attitude_religius'],
                            'attitude_social' => $data['attitude_social'],
                        ]; 
                        
                        return $records->each->update($dataUpdate);
                    }),
                ]),
            ])
            ->modifyQueryUsing(function(Builder $query){
                $query->myGrade();
            });
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
            'index' => Pages\ListAttitudes::route('/'),
            'create' => Pages\CreateAttitude::route('/create'),
            'edit' => Pages\EditAttitude::route('/{record}/edit'),
        ];
    }
}
