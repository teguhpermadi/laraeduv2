<?php

namespace App\Livewire;

use App\Models\Competency;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

class CompetencyReference extends Component implements HasForms, HasTable   
{
    use InteractsWithForms;
    use InteractsWithTable;

    public $teacherSubjects;

    public function mount($teacherSubjects)
    {
        $this->teacherSubjects = $teacherSubjects;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Competency::query())
            ->columns([
                // TextColumn::make('code'),
                TextColumn::make('description')
                    ->wrap()
                    ->copyable()
                    ->copyMessage('Disalin'),
                // TextColumn::make('teacherSubject.subject.name'),
                // TextColumn::make('teacherSubject.grade.name')
            ])
            ->filters([
                // ...
            ])
            ->actions([
                // ...
            ])
            ->bulkActions([
                // ...
            ])->modifyQueryUsing(function (Builder $query) {
                $query->where('teacher_subject_id', $this->teacherSubjects);
            })
            ->paginated(false);
    }

    public function render()
    {
        return view('livewire.competency-reference');
    }
}
