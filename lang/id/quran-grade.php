<?php

return [
    'resource' => [
        'label' => 'Kelas Quran',
        'plural_label' => 'Kelas-kelas Quran',
    ],

    'fields' => [
        'name' => [
            'label' => 'Nama',
            'placeholder' => 'Masukkan nama kelas',
        ],
        'level' => [
            'label' => 'Level',
            'placeholder' => 'Masukkan level',
        ],
        'students' => [
            'label' => 'Siswa Mengaji',
        ],
        'teacher' => [
            'label' => 'Guru Mengaji',
        ],
    ],

    'messages' => [
        'created' => 'Tingkat Quran berhasil dibuat',
        'updated' => 'Tingkat Quran berhasil diperbarui',
        'deleted' => 'Tingkat Quran berhasil dihapus',
    ],

    'actions' => [
        'create' => 'Buat Tingkat',
        'edit' => 'Edit',
        'delete' => 'Hapus',
    ],
]; 