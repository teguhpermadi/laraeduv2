<?php

return [
    'navigation_label' => 'Kompetensi Quran',
    'model_label' => 'Kompetensi Mengaji',
    'plural_model_label' => 'Kompetensi Mengaji',
    'fields' => [
        'academic_year_id' => 'Tahun Akademik',
        'teacher_id' => 'Guru',
        'teacher_quran_grade_id' => 'ID Grade Guru Quran',
        'quran_grade_id' => 'Kelas Mengaji',
        'code' => 'Kode',
        'description' => 'Deskripsi',
        'passing_grade' => 'Nilai Kelulusan',
    ],
    'actions' => [
        'edit' => 'Ubah',
        'delete' => 'Hapus',
    ],
    'bulk_actions' => [
        'delete' => 'Hapus Terpilih',
    ],
    'pages' => [
        'index' => 'Daftar Kompetensi Quran',
        'create' => 'Buat Kompetensi Quran',
        'edit' => 'Ubah Kompetensi Quran',
    ],
];
