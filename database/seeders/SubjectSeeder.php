<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name'  => 'Pendidikan Pancasila',
                'code'  => 'PP',
                'order' => 1,
            ],
            [
                'name'  => 'Bahasa Indonesia',
                'code'  => 'BIN',
                'order' => 2,
            ],
            [
                'name'  => 'Matematika',
                'code'  => 'MTK',
                'order' => 3,
            ],
            [
                'name'  => 'Ilmu Pengetahuan dan Sosial',
                'code'  => 'IPAS',
                'order' => 4,
            ],
            [
                'name'  => 'Seni Budaya dan Prakarya',
                'code'  => 'SBDP',
                'order' => 5,
            ],
            [
                'name'  => 'Pendidikan Jasmani Olahraga dan Kesehatan',
                'code'  => 'PJOK',
                'order' => 6,
            ],
            [
                'name'  => 'Bahasa Jawa',
                'code'  => 'BJ',
                'order' => 7,
            ],
            [
                'name'  => 'Bahasa Inggris',
                'code'  => 'BIG',
                'order' => 8,
            ],
            [
                'name'  => 'Akidah Akhlak',
                'code'  => 'AA',
                'order' => 9,
            ],
            [
                'name'  => 'Fiqih',
                'code'  => 'FQ',
                'order' => 10,
            ],
            [
                'name'  => 'Al Quran Hadist',
                'code'  => 'QH',
                'order' => 11,
            ],
            [
                'name'  => 'Sejarah Kebudayaan Islam',
                'code'  => 'SKI',
                'order' => 12,
            ],
        ];

        Subject::insert($data);
    }
}
