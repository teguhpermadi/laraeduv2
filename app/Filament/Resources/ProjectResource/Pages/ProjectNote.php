<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Models\Project;
use App\Models\ProjectNote as ModelsProjectNote;
use App\Models\StudentGrade;
// action header
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ProjectNote extends Page implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static string $resource = ProjectResource::class;

    protected static string $view = 'filament.resources.project-resource.pages.project-note';

    public $projectId = -1;
    public $record;

    public function mount($record)
    {
        $this->projectId = $record;
        $this->record = $record;
    }

    public function table(Table $table): Table
    {
        return $table
                ->query(ModelsProjectNote::query())
                ->columns([
                    TextColumn::make('student.name')
                        ->label(__('project.student')),
                    TextInputColumn::make('note')
                        ->label(__('project.note')),
                ])
                ->bulkActions([
                    BulkAction::make('scoring')
                        ->label(__('project.scoring'))
                        ->form([
                            TextInput::make('note')
                                ->label(__('project.note'))
                        ])
                        ->action(function (Collection $records, $data) {
                            $records->each->update($data);
                        })
                ])
                ->modifyQueryUsing(function (Builder $query){
                    $query->where('project_id', $this->projectId);
                })
                ->paginated(false)
                ->headerActions([
                    Action::make('resetNote')
                        ->label(__('project.resetNote'))
                        ->action(function () {
                            $this->resetNote();
                        })
                ]);
    }

    public function resetNote()
    {
        // ambil detil project berdasarkan id
        $project = Project::find($this->record);
        // cek terlebih dahulu student_id berdasarkan grade_id dari project
        $students = StudentGrade::where('grade_id', $project->grade_id)->get();
        // tambahkan student_id ke dalam ProjectStudent apabila tidak ada
        foreach ($students as $student) {
            ModelsProjectNote::updateOrCreate([
                'academic_year_id' => session('academic_year_id'),
                'student_id' => $student->student_id,
                'project_id' => $this->projectId,
            ], [
                'note' => '',
            ]);
        }
    }
}
