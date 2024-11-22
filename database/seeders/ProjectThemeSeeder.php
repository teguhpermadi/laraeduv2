<?php

namespace Database\Seeders;

use App\Enums\SchoolLevelEnum;
use App\Models\ProjectTheme;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                "name" => "Aku Sayang Bumi",
                "level" => SchoolLevelEnum::PAUD->value
            ],
            [
                "name" => "Aku Cinta Indonesia",
                "level" => SchoolLevelEnum::PAUD->value
            ],
            [
                "name" => "Kita Semua Bersaudara",
                "level" => SchoolLevelEnum::PAUD->value
            ],
            [
                "name" => "Imajinasi dan Kreativitasku",
                "level" => SchoolLevelEnum::PAUD->value
            ],
            [
                "name" => "Gaya Hidup Berkelanjutan",
                "level" => SchoolLevelEnum::SD->value
            ],
            [
                "name" => "Kearifan Lokal",
                "level" => SchoolLevelEnum::SD->value
            ],
            [
                "name" => "Bhinneka Tunggal Ika",
                "level" => SchoolLevelEnum::SD->value
            ],
            [
                "name" => "Bangunlah Jiwa dan Raganya",
                "level" => SchoolLevelEnum::SD->value
            ],
            [
                "name" => "Rekayasa dan Teknologi",
                "level" => SchoolLevelEnum::SD->value
            ],
            [
                "name" => "Kewirausahaan",
                "level" => SchoolLevelEnum::SD->value
            ],
            [
                "name" => "Gaya Hidup Berkelanjutan",
                "level" => SchoolLevelEnum::SMP->value
            ],
            [
                "name" => "Kearifan Lokal",
                "level" => SchoolLevelEnum::SMP->value
            ],
            [
                "name" => "Bhinneka Tunggal Ika",
                "level" => SchoolLevelEnum::SMP->value
            ],
            [
                "name" => "Bangunlah Jiwa dan Raganya",
                "level" => SchoolLevelEnum::SMP->value
            ],
            [
                "name" => "Rekayasa dan Teknologi",
                "level" => SchoolLevelEnum::SMP->value
            ],
            [
                "name" => "Kewirausahaan",
                "level" => SchoolLevelEnum::SMP->value
            ],
            [
                "name" => "Gaya Hidup Berkelanjutan",
                "level" => SchoolLevelEnum::SMA->value
            ],
            [
                "name" => "Kearifan Lokal",
                "level" => SchoolLevelEnum::SMA->value
            ],
            [
                "name" => "Bhinneka Tunggal Ika",
                "level" => SchoolLevelEnum::SMA->value
            ],
            [
                "name" => "Bangunlah Jiwa dan Raganya",
                "level" => SchoolLevelEnum::SMA->value
            ],
            [
                "name" => "Rekayasa dan Teknologi",
                "level" => SchoolLevelEnum::SMA->value
            ],
            [
                "name" => "Kewirausahaan",
                "level" => SchoolLevelEnum::SMA->value
            ],
        ];
        $data = [
            [
                "name" => "Aku Sayang Bumi",
                "level" => "paud"
            ],
            [
                "name" => "Aku Cinta Indonesia",
                "level" => "paud"
            ],
            [
                "name" => "Kita Semua Bersaudara",
                "level" => "paud"
            ],
            [
                "name" => "Imajinasi dan Kreativitasku",
                "level" => "paud"
            ],
            [
                "name" => "Gaya Hidup Berkelanjutan",
                "level" => "sd"
            ],
            [
                "name" => "Kearifan Lokal",
                "level" => "sd"
            ],
            [
                "name" => "Bhinneka Tunggal Ika",
                "level" => "sd"
            ],
            [
                "name" => "Bangunlah Jiwa dan Raganya",
                "level" => "sd"
            ],
            [
                "name" => "Rekayasa dan Teknologi",
                "level" => "sd"
            ],
            [
                "name" => "Kewirausahaan",
                "level" => "sd"
            ],
            [
                "name" => "Gaya Hidup Berkelanjutan",
                "level" => "smp"
            ],
            [
                "name" => "Kearifan Lokal",
                "level" => "smp"
            ],
            [
                "name" => "Bhinneka Tunggal Ika",
                "level" => "smp"
            ],
            [
                "name" => "Bangunlah Jiwa dan Raganya",
                "level" => "smp"
            ],
            [
                "name" => "Suara Demokrasi",
                "level" => SchoolLevelEnum::SMP->value
            ],
            [
                "name" => "Rekayasa dan Teknologi",
                "level" => "smp"
            ],
            [
                "name" => "Kewirausahaan",
                "level" => "smp"
            ],
            [
                "name" => "Gaya Hidup Berkelanjutan",
                "level" => "sma"
            ],
            [
                "name" => "Kearifan Lokal",
                "level" => "sma"
            ],
            [
                "name" => "Bhinneka Tunggal Ika",
                "level" => "sma"
            ],
            [
                "name" => "Bangunlah Jiwa dan Raganya",
                "level" => "sma"
            ],
            [
                "name" => "Suara Demokrasi",
                "level" => "sma"
            ],
            [
                "name" => "Rekayasa dan Teknologi",
                "level" => "sma"
            ],
            [
                "name" => "Kewirausahaan",
                "level" => "sma"
            ],
            [
                "name" => "Gaya Hidup Berkelanjutan",
                "level" => "smk"
            ],
            [
                "name" => "Kearifan Lokal",
                "level" => "smk"
            ],
            [
                "name" => "Bhinneka Tunggal Ika",
                "level" => "smk"
            ],
            [
                "name" => "Bangunlah Jiwa dan Raganya",
                "level" => "smk"
            ],
            [
                "name" => "Suara Demokrasi",
                "level" => "smk"
            ],
            [
                "name" => "Rekayasa dan Teknologi",
                "level" => "smk"
            ],
            [
                "name" => "Kebekerjaan",
                "level" => "smk"
            ]
        ];

        ProjectTheme::insert($data);
    }
}