<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use App\Filament\Resources\StudentResource\Widgets\StudentWidget;
use App\Imports\StudentImport;
use Closure;
use Filament\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            \EightyNine\ExcelImport\ExcelImportAction::make()
                ->color("primary")
                ->closeModalByClickingAway(false)
                ->slideOver()
                ->use(StudentImport::class)
                ->sampleExcel(
                    sampleData: [
                        [
                            "nisn",
                            "nis",
                            "nama_lengkap",
                            "nama_panggilan",
                            "jenis_kelamin",
                            "tempat_lahir",
                            "tanggal_lahir",
                            "agama",
                            "asal_sekolah",
                            "tanggal_diterima",
                            "diterima_kelas",
                            "nama_ayah",
                            "pendidikan_ayah",
                            "pekerjaan_ayah",
                            "telp_ayah",
                            "nama_ibu",
                            "pendidikan_ibu",
                            "pekerjaan_ibu",
                            "telp_ibu",
                            "nama_wali",
                            "pendidikan_wali",
                            "pekerjaan_wali",
                            "telp_wali",
                            "alamat_wali",
                            "alamat_siswa",
                            "kelurahan_siswa",
                            "kecamatan_siswa",
                            "kota_siswa",
                            "provinsi_siswa",
                            "alamat_orangtua",
                            "kelurahan_orangtua",
                            "kecamatan_orangtua",
                            "kota_orangtua",
                            "provinsi_orangtua",
                        ]
                    ],
                    fileName: 'student-template.xlsx',
                    // exportClass: App\Exports\SampleExport::class, 
                    sampleButtonLabel: 'Download Template',
                    // customiseActionUsing: fn(Action $action) => $action->color('secondary')
                    //     ->icon('heroicon-m-clipboard')
                    //     ->requiresConfirmation(),
                ),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            StudentWidget::class,
        ];
    }
}
