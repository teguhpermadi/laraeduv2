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
            ],
            [
                'name'  => 'Bahasa Indonesia',
                'code'  => 'BIN',
            ],
            [
                'name'  => 'Matematika',
                'code'  => 'MTK',
            ],
            [
                'name'  => 'Ilmu Pengetahuan dan Sosial',
                'code'  => 'IPAS',
            ],
            [
                'name'  => 'Seni Budaya dan Prakarya',
                'code'  => 'SBDP',
            ],
            [
                'name'  => 'Pendidikan Jasmani Olahraga dan Kesehatan',
                'code'  => 'PJOK',
            ],
            [
                'name'  => 'Bahasa Jawa',
                'code'  => 'BJ',
            ],
            [
                'name'  => 'Bahasa Inggris',
                'code'  => 'BIG',
            ],
            [
                'name'  => 'Akidah Akhlak',
                'code'  => 'AA',
            ],
            [
                'name'  => 'Fiqih',
                'code'  => 'FQ',
            ],
            [
                'name'  => 'Al Quran Hadist',
                'code'  => 'QH',
            ],
            [
                'name'  => 'Sejarah Kebudayaan Islam',
                'code'  => 'SKI',
            ],
        ];

        Subject::insert($data);
    }
}
