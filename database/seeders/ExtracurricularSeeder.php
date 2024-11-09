<?php

namespace Database\Seeders;

use App\Models\Extracurricular;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExtracurricularSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //buat 5   data dummy
        Extracurricular::create([
            'name' => 'Basket',
            'is_required' => false,
        ]);

        Extracurricular::create([
            'name' => 'Voli',
            'is_required' => false,
        ]);

        Extracurricular::create([
            'name' => 'Paskibraka',
            'is_required' => false,
        ]);

        Extracurricular::create([
            'name' => 'Pramuka',
            'is_required' => true,
        ]);
    }
}
