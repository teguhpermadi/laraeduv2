<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // AcademicYear::factory(1)->create();

        $data = [
            'id' => Str::ulid()->toBase32(),
            'year' => '2024/2025',
            'semester' => 'ganjil',
            // 'teacher_id' => 1,
            'date_report_half' => now(),
            'date_report' => now(),
        ];

        AcademicYear::create($data);
    }
}
