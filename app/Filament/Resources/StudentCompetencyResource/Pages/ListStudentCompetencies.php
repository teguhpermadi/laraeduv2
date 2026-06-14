<?php

namespace App\Filament\Resources\StudentCompetencyResource\Pages;

use App\Filament\Resources\StudentCompetencyResource;
use App\Services\RdmExportZipService;
use Filament\Actions;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListStudentCompetencies extends ListRecords
{
    protected static string $resource = StudentCompetencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('upload')
                ->label('Upload Data')
                ->icon('heroicon-o-document-text')
                ->url(StudentCompetencyResource::getUrl('upload')),
            Actions\Action::make('exportRdm')
                ->label('Export to RDM')
                ->icon('heroicon-o-archive-box-arrow-down')
                ->color('success')
                ->action('exportAllRdm'),
        ];
    }

    public function exportAllRdm(): void
    {
        $academicYearId = session()->get('academic_year_id');

        if (! $academicYearId) {
            Notification::make()
                ->title('Gagal')
                ->body('Tahun akademik tidak ditemukan. Silakan pilih tahun akademik terlebih dahulu.')
                ->danger()
                ->send();

            return;
        }

        try {
            set_time_limit(300);

            $service = app(RdmExportZipService::class);
            $zipFilename = $service->export($academicYearId);

            $downloadUrl = route('rdm-export.download', ['file' => $zipFilename]);

            Notification::make()
                ->title('Export RDM Berhasil')
                ->body('Semua file RDM berhasil diexport. Klik tombol di bawah untuk mendownload.')
                ->success()
                ->actions([
                    Action::make('download')
                        ->label('Download ZIP')
                        ->url($downloadUrl)
                        ->openUrlInNewTab(),
                ])
                ->send()
                ->sendToDatabase(auth()->user());
        } catch (\Throwable $e) {
            report($e);

            Notification::make()
                ->title('Export Gagal')
                ->body('Terjadi kesalahan: '.$e->getMessage())
                ->danger()
                ->send()
                ->sendToDatabase(auth()->user());
        }
    }
}
