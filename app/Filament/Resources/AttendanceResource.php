<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceResource\Pages;
use App\Filament\Resources\AttendanceResource\RelationManagers;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return __('attendance.attendance');
    }

    protected static ?string $navigationGroup = 'Kelas Ku';

    protected static ?int $navigationSort = 2;  

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('academic_year_id')->default(session()->get('academic_year_id')),
                Hidden::make('grade_id'),
                Select::make('student_id')
                    ->label(__('student.name'))
                    ->reactive()
                    ->required()
                    ->options(Student::myStudentGrade()->pluck('name', 'id'))
                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                        $grade_id = Student::find($state)->studentGrade->first()->grade_id;
                        // dd($grade_id);
                        $set('grade_id', $grade_id);
                    }),
                TextInput::make('sick')
                    ->label(__('attendance.sick'))
                    ->numeric()
                    ->default(0),
                TextInput::make('permission')
                    ->label(__('attendance.permission'))
                    ->numeric()
                    ->default(0),
                TextInput::make('absent')
                    ->label(__('attendance.absent'))
                    ->numeric()
                    ->default(0),
                TextInput::make('note')
                    ->label(__('attendance.note')),
                TextInput::make('achievement')
                    ->label(__('attendance.achievement')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.name')
                    ->searchable()
                    ->label(__('student.name')),
                // kolom naik kelas / tinggal kelas
                SelectColumn::make('status')
                    ->label(__('attendance.status'))
                    ->options([
                        1 => 'NAIK KELAS',
                        0 => 'TINGGAL KELAS',
                    ])
                    // hidden jika semester ganjil
                    ->hidden(AcademicYear::query()->find(session()->get('academic_year_id'))->semester == 'ganjil'),
                TextInputColumn::make('sick')
                    ->label(__('attendance.sick'))
                    ->rules(['numeric']),
                TextInputColumn::make('permission')
                    ->label(__('attendance.permission'))
                    ->rules(['numeric']),
                TextInputColumn::make('absent')
                    ->label(__('attendance.absent'))
                    ->rules(['numeric']),
                // TextInputColumn::make('note'),
                // TextInputColumn::make('achievement'),
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
                ]),
            ])
            ->modifyQueryUsing(
                fn(Builder $query) => $query->myGrade()
            );
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
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
        ];
    }
}
