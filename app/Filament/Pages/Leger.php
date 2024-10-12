<?php

namespace App\Filament\Pages;

use App\Models\TeacherSubject;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as SectionInfoList;
use Filament\Pages\Page;

class Leger extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $slug = 'leger/{id}';

    protected static string $view = 'filament.pages.leger';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public TeacherSubject $record;

    public ?array $data = [];

    public $teacherSubject;

    public function mount($id): void
    {
        $this->teacherSubject = TeacherSubject::find($id);
        
    }

    public function form(Form $form):Form 
    {
        return $form
            ->schema([
                Section::make('preview')
                    ->schema([
                        ViewField::make('preview')
                        ->viewData([$this->teacherSubject])
                        ->view('filament.pages.leger-preview')
                    ]),
            ]);
    }
}
