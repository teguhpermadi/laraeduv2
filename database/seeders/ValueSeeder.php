<?php

namespace Database\Seeders;

use App\Models\Value;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                "code_dimention" => "1",
                "code_element" => "1.1",
                "code" => "1.1.1",
                "description" => "Berkeadaban (Ta’addub)"
            ],
            [
                "code_dimention" => "1",
                "code_element" => "1.2",
                "code" => "1.2.1",
                "description" => "Berkeadaban (Ta’addub)"
            ],
            [
                "code_dimention" => "1",
                "code_element" => "1.2",
                "code" => "1.2.2",
                "description" => "Keteladanan (Qudwah)"
            ],
            [
                "code_dimention" => "1",
                "code_element" => "1.3",
                "code" => "1.3.1",
                "description" => "Berkeadaban (Ta’addub)"
            ],
            [
                "code_dimention" => "1",
                "code_element" => "1.3",
                "code" => "1.3.2",
                "description" => "Kesetaraan (Musāwah)"
            ],
            [
                "code_dimention" => "1",
                "code_element" => "1.4",
                "code" => "1.4.1",
                "description" => "Berkeadaban (Ta’addub)"
            ],
            [
                "code_dimention" => "1",
                "code_element" => "1.4",
                "code" => "1.4.2",
                "description" => "Dinamis dan inovatif (Tathawwur wa Ibtikâr)"
            ],
            [
                "code_dimention" => "1",
                "code_element" => "1.5",
                "code" => "1.5.1",
                "description" => "Kewarganegaraan dan kebangsaan (Muwaṭanah)"
            ],
            [
                "code_dimention" => "2",
                "code_element" => "2.1",
                "code" => "2.1.1",
                "description" => "Kewarganegaraan dan kebangsaan (Muwaṭanah)"
            ],
            [
                "code_dimention" => "2",
                "code_element" => "2.2",
                "code" => "2.2.1",
                "description" => "Musyawarah (Syūra)"
            ],
            [
                "code_dimention" => "2",
                "code_element" => "2.3",
                "code" => "2.3.1",
                "description" => "Kewarganegaraan dan kebangsaan (Muwaṭanah)"
            ],
            [
                "code_dimention" => "2",
                "code_element" => "2.4",
                "code" => "2.4.1",
                "description" => "Adil dan Konsisten (I’tidāl)"
            ],
            [
                "code_dimention" => "2",
                "code_element" => "2.4",
                "code" => "2.4.2",
                "description" => "Musyawarah (Syūra)"
            ],
            [
                "code_dimention" => "3",
                "code_element" => "3.1",
                "code" => "3.1.1",
                "description" => "Toleransi (Tasāmuh)"
            ],
            [
                "code_dimention" => "3",
                "code_element" => "3.2",
                "code" => "3.2.1",
                "description" => "Toleransi (Tasāmuh)"
            ],
            [
                "code_dimention" => "3",
                "code_element" => "3.3",
                "code" => "3.3.1",
                "description" => "Toleransi (Tasāmuh)"
            ],
            [
                "code_dimention" => "4",
                "code_element" => "4.1",
                "code" => "4.1.1",
                "description" => "Keteladanan (Qudwah)"
            ],
            [
                "code_dimention" => "4",
                "code_element" => "4.2",
                "code" => "4.2.1",
                "description" => "Keteladanan (Qudwah)"
            ],
            [
                "code_dimention" => "5",
                "code_element" => "5.1",
                "code" => "5.1.1",
                "description" => "Dinamis dan inovatif (Tathawwur wa Ibtikâr)"
            ],
            [
                "code_dimention" => "5",
                "code_element" => "5.2",
                "code" => "5.2.1",
                "description" => "Dinamis dan inovatif (Tathawwur wa Ibtikâr)"
            ],
            [
                "code_dimention" => "5",
                "code_element" => "5.3",
                "code" => "5.3.1",
                "description" => "Dinamis dan inovatif (Tathawwur wa Ibtikâr)"
            ],
            [
                "code_dimention" => "6",
                "code_element" => "6.1",
                "code" => "6.1.1",
                "description" => "Dinamis dan inovatif (Tathawwur wa Ibtikâr)"
            ],
            [
                "code_dimention" => "6",
                "code_element" => "6.2",
                "code" => "6.2.1",
                "description" => "Dinamis dan inovatif (Tathawwur wa Ibtikâr)"
            ],
            [
                "code_dimention" => "6",
                "code_element" => "6.3",
                "code" => "6.3.1",
                "description" => "Dinamis dan inovatif (Tathawwur wa Ibtikâr)"
            ]
        ];

        foreach ($data as $key => $value) {
            Value::create($value);
        }
    }
}