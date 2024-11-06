<?php

namespace Database\Seeders;

use App\Models\Dimention;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DimentionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'code' => 1,
                'description' => 'Beriman, Bertakwa Kepada Tuhan Yang Maha Esa, dan Berakhlak Mulia'
            ],
            [
                'code' => 2,
                'description' => 'Berkebhinekaan Global'
            ],
            [
                'code' => 3,
                'description' => 'Bergotong-Royong'
            ],
            [
                'code' => 4,
                'description' => 'Mandiri'
            ],
            [
                'code' => 5,
                'description' => 'Bernalar Kritis'
            ],
            [
                'code' => 6,
                'description' => 'Kreatif'
            ],
        ];

        Dimention::insert($data);
    }
}