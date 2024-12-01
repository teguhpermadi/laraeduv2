<?php

namespace Database\Seeders;

use App\Models\TeacherSubject;
use App\Models\Competency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeacherSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kompetensi_siswa = [
            // Kelas 1
            'Mengenal huruf hijaiyah dan bacaan doa sehari-hari',
            'Menghafal bacaan sholat dan doa',
            'Mengenal angka dan bilangan 1-100',
            'Menghitung penjumlahan dan pengurangan sederhana',
            'Menulis angka dan huruf dengan rapi',
            'Mengetahui nama-nama hari dan bulan',
            'Menentukan waktu sholat berdasarkan posisi matahari',
            'Mengenal nama-nama anggota tubuh',
            'Menghitung benda-benda yang ada di sekitar',
            'Mengenal sifat-sifat Allah dalam Al-Qur\'an',
        
            // Kelas 2
            'Menghafal doa-doa pendek untuk kegiatan sehari-hari',
            'Menghitung perkalian dan pembagian sederhana',
            'Mengenal bentuk geometri (lingkaran, segitiga, persegi, dll)',
            'Menulis dan membaca teks sederhana',
            'Menulis cerita pendek berdasarkan pengalaman',
            'Menghitung waktu dengan menggunakan jam',
            'Mengenal hewan dan tumbuhan di sekitar lingkungan',
            'Menjaga kebersihan dan kesehatan diri',
            'Mengetahui cara-cara menjaga kebersihan lingkungan',
            'Mengenal adat istiadat di Indonesia',
        
            // Kelas 3
            'Membaca dan menulis teks bacaan yang lebih kompleks',
            'Menghitung bilangan desimal dan pecahan sederhana',
            'Menulis surat atau pesan singkat secara sederhana',
            'Memahami pentingnya saling menghormati sesama',
            'Menggunakan bahasa Indonesia yang benar dalam percakapan sehari-hari',
            'Mengetahui jenis-jenis pekerjaan di masyarakat',
            'Mengenal produk budaya daerah di Indonesia',
            'Menghitung luas dan keliling bangun datar sederhana',
            'Mengetahui ciri-ciri makhluk hidup dan cara berkembang biaknya',
            'Mengenal siklus air dalam kehidupan',
        
            // Kelas 4
            'Membaca teks non-fiksi dengan memahami isi dan maksudnya',
            'Membaca dan menghafal surat-surat pendek dalam Al-Qur\'an',
            'Menghitung bilangan bulat, pecahan, dan desimal dengan aplikasi dalam kehidupan',
            'Menulis laporan sederhana berdasarkan observasi',
            'Menghitung volume bangun ruang sederhana (kubus, balok, dll)',
            'Mengenal klasifikasi benda berdasarkan sifat fisiknya',
            'Mengetahui berbagai jenis transportasi dan fungsinya',
            'Menjelaskan proses fotosintesis pada tumbuhan',
            'Mengenal jenis-jenis makanan sehat dan bergizi',
            'Mempelajari perilaku sosial yang baik dalam masyarakat',
        
            // Kelas 5
            'Membaca teks naratif dan diskursif',
            'Menulis teks naratif, eksposisi, dan deskripsi',
            'Menghitung peluang dan statistik sederhana',
            'Menggunakan sistem satuan panjang, massa, dan waktu dalam perhitungan',
            'Mempelajari konsep-konsep dalam geometri seperti simetri dan transformasi',
            'Mengenal sistem pencernaan manusia dan hewan',
            'Memahami pentingnya menjaga lingkungan hidup',
            'Mengetahui pentingnya air bagi kehidupan',
            'Mengenal cuaca dan iklim serta dampaknya terhadap kehidupan',
            'Membuat karya seni sederhana dari bahan alam',
        
            // Kelas 6
            'Menguasai bacaan Al-Qur\'an dengan tajwid yang benar',
            'Membaca dan menulis teks fiksi dengan memahami struktur cerita',
            'Menggunakan operasi hitung dalam kehidupan sehari-hari (pajak, belanja, dll)',
            'Memahami konsep kebudayaan dan adat istiadat di Indonesia',
            'Menghitung luas dan volume bangun ruang lebih kompleks (tabung, kerucut, dll)',
            'Menghitung besaran fisis dalam kehidupan sehari-hari',
            'Menggunakan teknologi dalam pembelajaran dan kehidupan sehari-hari',
            'Membuat karya ilmiah sederhana berdasarkan hasil penelitian',
            'Memahami etika dan moral dalam kehidupan sosial',
            'Menghargai perbedaan dan menjaga kerukunan antar umat beragama',
        
            // Kompetensi Umum (untuk semua kelas)
            'Mengembangkan keterampilan berbicara dan berkomunikasi dengan baik',
            'Mengembangkan keterampilan mendengarkan dan memahami informasi',
            'Mengembangkan keterampilan menulis dengan rapi dan jelas',
            'Menghargai keberagaman budaya dan agama di masyarakat',
            'Menghormati orang tua, guru, dan sesama teman',
            'Mengenal pentingnya pendidikan karakter dalam kehidupan sehari-hari',
            'Membangun sikap disiplin, jujur, dan tanggung jawab',
            'Mempelajari cara-cara menjaga keselamatan diri dan orang lain',
            'Memahami pentingnya kebersihan diri dan lingkungan',
            'Menerapkan sikap sabar dan tidak cepat marah dalam interaksi sosial',
        ];

        // Buat data factory
        $data = TeacherSubject::factory(20)->make()->toArray();

        // Tambahkan satu per satu agar observer berjalan
        foreach ($data as $item) {
            // gunakan updateOrCreate agar observer berjalan
            TeacherSubject::updateOrCreate(
                [
                    'academic_year_id' => $item['academic_year_id'],
                    'grade_id' => $item['grade_id'],
                    'subject_id' => $item['subject_id'],
                ],
                [
                    'teacher_id' => $item['teacher_id'],
                    'time_allocation' => $item['time_allocation'],
                ]
            );
        }

        // Tambahkan kompetensi untuk setiap teacher subject
        TeacherSubject::all()->each(function ($teacherSubject) use ($kompetensi_siswa) {
            // 2 kompetensi dengan half_semester true
            for ($i = 1; $i <= 2; $i++) {
                Competency::create([
                    'teacher_subject_id' => $teacherSubject->id,
                    'code' => 'KD' . $i . '-S1',
                    'description' => fake()->randomElement($kompetensi_siswa),
                    'passing_grade' => 75,
                    'half_semester' => true,
                ]);
            }

            // 2 kompetensi dengan half_semester false
            for ($i = 1; $i <= 2; $i++) {
                Competency::create([
                    'teacher_subject_id' => $teacherSubject->id,
                    'code' => 'KD' . $i . '-S2',
                    'description' => fake()->randomElement($kompetensi_siswa),
                    'passing_grade' => 75,
                    'half_semester' => false,
                ]);
            }
        });
    }
}
