<?php

namespace App\Console\Commands;

use App\Models\AcademicYear;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CopyAcademicYearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:copy-academic-year';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy academic year from laraedu to laraeduv2';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $academicYears = DB::connection('laraedu')->table('academic_years')->get();

        foreach ($academicYears as $academicYear) {
            $array = json_decode(json_encode($academicYear), true);

            $data = [
                'id' => $array['ulid'],
                'year' => $array['year'],
                'semester' => $array['semester'],
                'teacher_id' => $array['teacher_ulid'],
                'date_report_half' => $array['date_report_half'],
                'date_report' => $array['date_report'],
            ];

            try {
                //code...
                AcademicYear::create($data);
            } catch (\Throwable $th) {
                //throw $th;
                Log::error($th);
            }
        }
    }
}
