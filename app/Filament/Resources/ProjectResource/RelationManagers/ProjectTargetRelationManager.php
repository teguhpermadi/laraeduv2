<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Step;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use App\Models\Dimention;
use App\Models\Element;
use App\Models\SubElement;
use App\Models\Value;
use App\Models\Target;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step as WizardStep;

class ProjectTargetRelationManager extends RelationManager
{
    protected static string $relationship = 'ProjectTarget';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Hidden::make('project_id')
                    ->default(function (RelationManager $livewire) {
                        return $livewire->getOwnerRecord()->id;
                    }),
                Hidden::make('phase')
                    ->default(function (RelationManager $livewire) {
                        return $livewire->getOwnerRecord()->phase;
                    }),
                Wizard::make([
                    WizardStep::make('Dimensi')
                        ->schema([
                            Radio::make('dimention_id')
                                ->options(Dimention::pluck('description', 'id'))
                                ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                    $element = $get('element');
                                    if ($element) {
                                        $set('element_id', null);
                                        $set('sub_element_id', null);
                                        $set('value_id', null);
                                        $set('sub_value_id', null);
                                    }
                                })
                                ->required()
                                ->reactive(),
                        ]),
                    WizardStep::make('Elemen')
                        ->schema([
                            Radio::make('element_id')
                                ->options(function ($state, callable $get, callable $set) {
                                    $dimentionId = $get('dimention_id');
                                    if ($dimentionId) {
                                        return Dimention::with('element')->find($dimentionId)->element->pluck('description', 'id');
                                    }
                                    return [];
                                })
                                ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                    $subElementId = $get('sub_element_id');
                                    if ($subElementId) {
                                        $set('sub_element_id', null);
                                        $set('sub_value', null);
                                    }
                                })
                                ->required()
                                ->reactive(),
                        ]),
                    WizardStep::make('Sub Elemen')
                        ->schema([
                            Radio::make('sub_element_id')
                                ->options(function ($state, callable $get, callable $set) {
                                    $elementId = $get('element_id');
                                    if ($elementId) {
                                        return Element::with('subElement')->find($elementId)->subElement->pluck('description', 'id');
                                    }
                                    return [];
                                })
                                ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                    $valueId = $get('value_id');
                                    if ($valueId) {
                                        $set('value_id', null);
                                    }

                                    // set target_id
                                    // $phase = $get('project_id');
                                    // dd($state);
                                    $phase = $get('phase');
                                    $codeSubElement = SubElement::find($state);
                                    $target = Target::where('code_sub_element', $codeSubElement->code)->where('phase', $phase)->first();
                                    // dd($target);
                                    $set('target_id', $target->id);
                                    $set('target', $target->description);
                                })
                                ->required()
                                ->reactive(),
                        ]),
                    WizardStep::make('Nilai')
                        ->schema([
                            Radio::make('value_id')
                                ->options(function ($state, callable $get, callable $set) {
                                    $elementId = $get('element_id');
                                    if ($elementId) {
                                        return Element::with('value')->find($elementId)->value->pluck('description', 'id');
                                    }
                                    return [];
                                })
                                ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                    $subValueId = $get('sub_value_id');
                                    if ($subValueId) {
                                        $set('sub_value_id', null);
                                    }
                                })
                                ->required()
                                ->reactive(),
                        ]),
                    WizardStep::make('Sub Nilai')
                        ->schema([
                            Radio::make('sub_value_id')
                                ->options(function ($state, callable $get, callable $set) {
                                    $valueId = $get('value_id');
                                    if ($valueId) {
                                        return Value::with('subValue')->find($valueId)->subValue->pluck('description', 'id');
                                    }
                                    return [];
                                })
                                ->required()
                                ->reactive(),
                        ]),
                    // Step::make('target')
                    // ->schema([
                    // ]),
                ])
                    ->columnSpanFull(),
                Hidden::make('target_id')
                    ->afterStateHydrated(function ($state, callable $set, callable $get) {
                        if ($get('target_id')) {
                            $descTarget = Target::find($state)->description;
                            $set('target', $descTarget);
                        }
                    }),
                Textarea::make('target')
                    ->disabled()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('dimention')
            ->columns([
                Tables\Columns\TextColumn::make('dimention.description')->wrap(),
                Tables\Columns\TextColumn::make('element.description')->wrap(),
                Tables\Columns\TextColumn::make('subElement.description')->wrap(),
                Tables\Columns\TextColumn::make('value.description')->wrap(),
                Tables\Columns\TextColumn::make('subValue.description')->wrap(),
                Tables\Columns\TextColumn::make('target.description')->wrap(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                // group action
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
