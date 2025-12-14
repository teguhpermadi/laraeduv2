<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompetencyQuranResource\Pages;
use App\Filament\Resources\CompetencyQuranResource\RelationManagers;
use App\Models\CompetencyQuran;
use App\Models\TeacherQuranGrade;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompetencyQuranResource extends Resource
{
    protected static ?string $model = CompetencyQuran::class;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('teacherQuranGrade', function ($query) {
                $query->where('academic_year_id', session()->get('academic_year_id'));
            });
    }

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // buatkan navigation group
    protected static ?string $navigationGroup = 'Mengaji';

    // model name
    protected static ?string $modelLabel = 'Kompetensi Mengaji';
    // plural model label
    protected static ?string $pluralModelLabel = 'Kompetensi Mengaji';

    // navigasi order
    protected static ?int $navigationSort = 3;


    // buatkan navigation label
    public static function getNavigationLabel(): string
    {
        return 'Kompetensi Quran';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('academic_year_id')
                    ->default(session()->get('academic_year_id')),
                Hidden::make('teacher_id')
                    ->default(auth()->user()->userable->userable_id),
                Hidden::make('teacher_quran_grade_id'),
                Select::make('quran_grade_id')
                    ->required()
                    ->label(__('competency-quran.fields.quran_grade_id'))
                    ->options(function () {
                        $options = TeacherQuranGrade::myQuranGrade()->with('quranGrade')->get()->pluck('quranGrade.name', 'id');
                        return $options;
                    })
                    ->live()
                    ->reactive()
                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                        // $teacherQuranGrade = TeacherQuranGrade::myQuranGrade()->where('quran_grade_id', $get('quran_grade_id'))->first();
                        $set('teacher_quran_grade_id', $state);
                    }),
                TextInput::make('code')
                    ->label(__('competency-quran.fields.code'))
                    ->helperText('Kode kompetensi harus unik dan tidak boleh sama dengan kompetensi lain yang Anda miliki')
                    ->required(),
                Textarea::make('description')
                    ->label(__('competency-quran.fields.description'))
                    ->required(),
                TextInput::make('passing_grade')
                    ->label(__('competency-quran.fields.passing_grade'))
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label(__('competency-quran.fields.code')),
                TextColumn::make('description')
                    ->label(__('competency-quran.fields.description')),
                TextColumn::make('passing_grade')
                    ->label(__('competency-quran.fields.passing_grade')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label(__('competency-quran.actions.delete')),
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
            'index' => Pages\ListCompetencyQurans::route('/'),
            'create' => Pages\CreateCompetencyQuran::route('/create'),
            'edit' => Pages\EditCompetencyQuran::route('/{record}/edit'),
        ];
    }
}
