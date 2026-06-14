<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeacherSubjectResource\Pages;
use App\Models\TeacherSubject;
use App\Services\LegerSubmitService;
use App\Services\LegerSyncCheckService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class TeacherSubjectResource extends Resource
{
    protected static ?string $model = TeacherSubject::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('academic_year_id')
                    ->required()
                    ->maxLength(26),
                Forms\Components\TextInput::make('grade_id')
                    ->required()
                    ->maxLength(26),
                Forms\Components\TextInput::make('teacher_id')
                    ->required()
                    ->maxLength(26),
                Forms\Components\TextInput::make('subject_id')
                    ->required()
                    ->maxLength(26),
                Forms\Components\TextInput::make('time_allocation')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('passing_grade')
                    ->required()
                    ->numeric()
                    ->default(70),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Tables\Columns\TextColumn::make('academic.year')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('grade.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('teacher.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('subject.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('competency_count')
                    ->label('Jumlah Kompetensi')
                    ->counts('competency'),
                Tables\Columns\TextColumn::make('passing_grade')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('syncLeger')
                    ->label(fn(TeacherSubject $record): string => app(LegerSyncCheckService::class)->overallNeedsSync($record) ? 'Sync' : 'Tersinkronisasi')
                    ->color(fn(TeacherSubject $record): string => app(LegerSyncCheckService::class)->overallNeedsSync($record) ? 'danger' : 'success')
                    ->icon(fn(TeacherSubject $record): string => app(LegerSyncCheckService::class)->overallNeedsSync($record) ? 'heroicon-o-arrow-path' : 'heroicon-o-check-circle')
                    ->disabled(fn(TeacherSubject $record): bool => ! app(LegerSyncCheckService::class)->overallNeedsSync($record))
                    ->action(function (TeacherSubject $record) {
                        try {
                            app(LegerSubmitService::class)->syncTeacherSubject($record);
                            Notification::make()
                                ->title('Berhasil')
                                ->body('Leger berhasil disinkronisasi')
                                ->success()
                                ->send();
                        } catch (\RuntimeException $e) {
                            Notification::make()
                                ->title('Gagal')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('syncLegerBulk')
                        ->label('Sync Leger')
                        ->icon('heroicon-o-arrow-path')
                        ->action(function (Collection $records) {
                            $synced = 0;
                            $skipped = 0;

                            foreach ($records as $record) {
                                if (! app(LegerSyncCheckService::class)->overallNeedsSync($record)) {
                                    $skipped++;

                                    continue;
                                }

                                try {
                                    app(LegerSubmitService::class)->syncTeacherSubject($record);
                                    $synced++;
                                } catch (\RuntimeException) {
                                    //
                                }
                            }

                            Notification::make()
                                ->title("Berhasil sync {$synced} data")
                                ->body($skipped > 0 ? "{$skipped} data sudah tersinkronisasi" : null)
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
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
            'index' => Pages\ListTeacherSubjects::route('/'),
            'create' => Pages\CreateTeacherSubject::route('/create'),
            'edit' => Pages\EditTeacherSubject::route('/{record}/edit'),
        ];
    }
}
