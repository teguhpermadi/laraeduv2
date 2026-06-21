<?php

namespace App\Filament\Widgets;

use App\Models\AcademicYear;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Widgets\Widget;

class AcademicYearSwitcher extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.widgets.academic-year-switcher';

    protected int|string|array $columnSpan = 'full';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('academic_year_id')
                    ->label('Tahun Akademik')
                    ->options(AcademicYear::all()->map(fn ($item) => [
                        'id' => $item->id,
                        'year' => $item->year.' - '.$item->semester,
                    ])->pluck('year', 'id'))
                    ->default(session()->get('academic_year_id'))
                    ->required(),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        session()->put('academic_year_id', $this->form->getState()['academic_year_id']);

        $this->redirect(request()->header('Referer', url('/admin/reports')));
    }
}
