<?php

declare(strict_types=1);

return[
    'list' => [
        'teacher' => 'Guru',
        'name' => 'Nama',
        'gender' => 'Jenis Kelamin',
    ],
    'create' => [
        'teacher' => 'Guru',
        'name' => 'Nama',
        'gender' => 'Jenis Kelamin',
        'nip' => 'NIP',
        'nuptk' => 'NUPTK',
        'signature' => 'Tanda Tangan'
    ],
    'list' => [
        'teacher' => 'Guru',
        'name' => 'Nama',
        'gender' => 'Jenis Kelamin',
        'nip' => 'NIP',
        'nuptk' => 'NUPTK',
        'signature' => 'Tanda Tangan'
    ],
    'relation' => [
        'subjects' => [
            'title' => 'Mata Pelajaran',
            'academic_year' => 'Tahun Akademik',
            'subject' => 'Mata Pelajaran',
            'grade' => 'Kelas',
            'time_allocation' => 'Alokasi Waktu',
        ],
        'teacher_grades' => [
            'title' => 'Guru Kelas',
            'grade' => 'Kelas',
            'status' => 'Status',
        ]
    ]
];