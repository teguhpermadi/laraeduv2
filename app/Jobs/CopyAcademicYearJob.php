<?php

namespace App\Jobs;

use App\Models\AcademicYear;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CopyAcademicYearJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        // Anda bisa menambahkan parameter di sini jika diperlukan
    }

    public function handle()
    {
        // Ambil data dari database laraedu
        $academicYears = DB::connection('laraedu')->table('academic_years')->get();

        // Masukkan data ke database laraeduv2
        foreach ($academicYears as $year) {
            // atur ulang datanya
            $data = [
                'year' => $year->year,
                'semester' => $year->semester,
                'teacher_id' => $year->teacher_id,
                'date_report' => $year->date_report,
                'date_report_half' => $year->date_report_half,
            ];

            AcademicYear::create($data);
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error('CopyAcademicYearJob failed: ' . $exception->getMessage());
    }
}
