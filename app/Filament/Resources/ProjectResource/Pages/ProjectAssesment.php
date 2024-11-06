<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Models\ProjectStudent;
use App\Models\ProjectTarget;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ProjectAssesment extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string $resource = ProjectResource::class;

    protected static string $view = 'filament.resources.project-resource.pages.project-assesment';

    public $options = [];
    public $projectTargetId = -1;
    public $record, $target_id;

    public ?array $data = [];

    public function mount($record)
    {
        $targets = ProjectTarget::where('project_id', $record)->get();
        foreach ($targets as $target) {
            $this->options[$target->id] = $target->target->description;
        }
        $this->record = $record;
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Capaian')
                    ->label(__('project.target'))
                    ->schema([
                        Radio::make('target_id')
                            ->label(__('project.target'))
                            ->options($this->options)
                            ->required()
                            ->live()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $set('projectTargetId', $state);
                            }),
                    ]),
            ]);
    }

    public function submit()
    {
        // dd($this->form->getState());
        $this->projectTargetId = $this->form->getState()['target_id'];
        // dd($this->projectTargetId);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ProjectStudent::query())
            ->columns([
                TextColumn::make('student.name')
                    ->label(__('project.student')),
                SelectColumn::make('score')
                    ->label(__('project.score'))
                    ->options([
                        4 => 'Sangat Berkembang',
                        3 => 'Berkembang Sesuai Harapan',
                        2 => 'Sedang Berkembang',
                        1 => 'Mulai Berkembang',
                ])
            ])
            ->bulkActions([
                BulkAction::make('scoring')
                    ->label(__('project.scoring'))
                    ->form([
                        Select::make('score')
                            ->label(__('project.score'))
                            ->options([
                                4 => 'Sangat Berkembang',
                                3 => 'Berkembang Sesuai Harapan',
                                2 => 'Sedang Berkembang',
                                1 => 'Mulai Berkembang',
                            ])
                    ])
                    ->action(function (Collection $records, $data) {
                        $records->each->update($data);
                    })
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query->where('project_target_id', $this->projectTargetId);
            })
            ->paginated(false);
    }
}
