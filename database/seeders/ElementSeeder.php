<?php

namespace Database\Seeders;

use App\Models\Element;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ElementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'code_dimention' => 1,
                'code' => '1.1',
                'description' => 'Akhlak Beragama'
            ],
            [
                'code_dimention' => 1,
                'code' => '1.2',
                'description' => 'Akhlak Pribadi'
            ],
            [
                'code_dimention' => 1,
                'code' => '1.3',
                'description' => 'Akhlak Kepada Manusia'
            ],
            [
                'code_dimention' => 1,
                'code' => '1.4',
                'description' => 'Akhlak Kepada Alam'
            ],
            [
                'code_dimention' => 1,
                'code' => '1.5',
                'description' => 'Akhlak Bernegara'
            ],
            [
                'code_dimention' => 2,
                'code' => '2.1',
                'description' => 'Mengenal dan menghargai budaya'
            ],
            [
                'code_dimention' => 2,
                'code' => '2.2',
                'description' => 'Komunikasi dan interaksi antar budaya'
            ],
            [
                'code_dimention' => 2,
                'code' => '2.3',
                'description' => 'Refleksi dan bertanggung jawab terhadap pengalaman kebinekaan'
            ],
            [
                'code_dimention' => 2,
                'code' => '2.4',
                'description' => 'Berkeadilan Sosial'
            ],
            [
                'code_dimention' => 3,
                'code' => '3.1',
                'description' => 'Kolaborasi'
            ],
            [
                'code_dimention' => 3,
                'code' => '3.2',
                'description' => 'Kepedulian'
            ],
            [
                'code_dimention' => 3,
                'code' => '3.3',
                'description' => 'Berbagi'
            ],
            [
                'code_dimention' => 4,
                'code' => '4.1',
                'description' => 'Pemahaman diri dan situasi yang dihadapi'
            ],
            [
                'code_dimention' => 4,
                'code' => '4.2',
                'description' => 'Regulasi Diri'
            ],
            [
                'code_dimention' => 5,
                'code' => '5.1',
                'description' => 'Memperoleh dan memproses informasi dan gagasan'
            ],
            [
                'code_dimention' => 5,
                'code' => '5.2',
                'description' => 'Menganalisis dan mengevaluasi penalaran dan prosedurnya'
            ],
            [
                'code_dimention' => 5,
                'code' => '5.3',
                'description' => 'Refleksi pemikiran dan proses berpikir'
            ],
            [
                'code_dimention' => 6,
                'code' => '6.1',
                'description' => 'Menghasilkan gagasan yang orisinal'
            ],
            [
                'code_dimention' => 6,
                'code' => '6.2',
                'description' => 'Menghasilkan karya dan tindakan yang orisinal'
            ],
            [
                'code_dimention' => 6,
                'code' => '6.3',
                'description' => 'Memiliki keluwesan berpikir dalam mencari alternatif solusi permasalahan'
            ],
        ];
        
        Element::insert($data);
    }
}