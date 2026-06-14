<?php

namespace App\Filament\Resources\AcademicYearResource\Pages;

use App\Filament\Resources\AcademicYearResource;
use App\Services\DescriptionService;
use Filament\Actions;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\HtmlString;

class EditReportDescription extends EditRecord
{
    protected static string $resource = AcademicYearResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Template Deskripsi Pengetahuan (Knowledge)')
                    ->description('Atur template deskripsi untuk nilai pengetahuan/ materi setiap siswa.')
                    ->relationship('reportDescription')
                    ->schema([
                        Placeholder::make('knowledge_preview')
                            ->label(false)
                            ->content(function (Get $get) {
                                $template = $get('knowledge_template');
                                $service = app(DescriptionService::class);

                                return new HtmlString(view('filament.components.report-description-preview', [
                                    'preview' => $service->previewTemplate($template, 'knowledge'),
                                    'validation' => $service->validateTemplate($template, 'knowledge'),
                                ])->render());
                            }),
                        Textarea::make('knowledge_template')
                            ->label('Template Pengetahuan')
                            ->placeholder('Kosongkan untuk menggunakan template bawaan')
                            ->helperText(new HtmlString(view('filament.components.report-description-hint', ['type' => 'knowledge'])->render()))
                            ->rows(6)
                            ->autosize()
                            ->live(onBlur: true),
                    ]),

                Section::make('Template Deskripsi Keterampilan (Skill)')
                    ->description('Atur template deskripsi untuk nilai keterampilan setiap siswa.')
                    ->relationship('reportDescription')
                    ->schema([
                        Placeholder::make('skill_preview')
                            ->label(false)
                            ->content(function (Get $get) {
                                $template = $get('skill_template');
                                $service = app(DescriptionService::class);

                                return new HtmlString(view('filament.components.report-description-preview', [
                                    'preview' => $service->previewTemplate($template, 'skill'),
                                    'validation' => $service->validateTemplate($template, 'skill'),
                                ])->render());
                            }),
                        Textarea::make('skill_template')
                            ->label('Template Keterampilan')
                            ->placeholder('Kosongkan untuk menggunakan template bawaan')
                            ->helperText(new HtmlString(view('filament.components.report-description-hint', ['type' => 'skill'])->render()))
                            ->rows(6)
                            ->autosize()
                            ->live(onBlur: true),
                    ]),
            ]);
    }

    protected function getRedirectUrl(): ?string
    {
        return $this->getResource()::getUrl('edit-description', ['record' => $this->record]);
    }
}
