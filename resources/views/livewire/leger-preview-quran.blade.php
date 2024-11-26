<div>
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-6 rounded-lg shadow-lg mb-6">
        <h1 class="text-3xl font-bold text-white text-center mb-2">Leger Quran {{ $teacherQuranGrade->quranGrade->name }}
        </h1>
        <div class="bg-white/10 p-4 rounded-md">
            <table class="text-white w-full max-w-2xl mx-auto">
                <tr>
                    <td class="py-1 font-semibold">Tahun Pelajaran</td>
                    <td class="px-3">:</td>
                    <td>{{ $teacherQuranGrade->academicYear->year }}</td>
                </tr>
                <tr>
                    <td class="py-1 font-semibold">Semester</td>
                    <td class="px-3">:</td>
                    <td>{{ $teacherQuranGrade->academicYear->semester }}</td>
                </tr>
                <tr>
                    <td class="py-1 font-semibold">Kelas</td>
                    <td class="px-3">:</td>
                    <td>{{ $teacherQuranGrade->quranGrade->name }}</td>
                </tr>
                <tr>
                    <td class="py-1 font-semibold">Guru</td>
                    <td class="px-3">:</td>
                    <td>{{ $teacherQuranGrade->teacher->name }}</td>
                </tr>
                {{-- tampilkan tanggal cetak --}}
                <tr>
                    <td class="py-1 font-semibold">Tanggal Cetak</td>
                    <td class="px-3">:</td>
                    {{-- buat format tanggal menjadi 30 November 2024 --}}
                    <td>{{ $legerQuranRecap->first()->created_at->locale('id')->format('d F Y H:i') }}</td>
                </tr>
            </table>
        </div>
    </div>

    @php
        $no = 1;
    @endphp

    <table class="border-collapse border border-slate-400 mt-3" width="100%">
        <thead>
            <tr>
                <th rowspan="2" class="border border-slate-300 text-center">Nomor</th>
                <th rowspan="2" class="border border-slate-300 text-center">NIS</th>
                <th rowspan="2" class="border border-slate-300 text-center">Nama Lengkap</th>
                <th colspan="{{ $competency_count }}" class="border border-slate-300 text-center">Nilai Kompetensi</th>
                <th rowspan="2" class="border border-slate-300 text-center">Jumlah</th>
                <th rowspan="2" class="border border-slate-300 text-center">Rata-rata</th>
                <th rowspan="2" class="border border-slate-300 text-center">Peringkat</th>
            </tr>
            @if ($competency_count > 0)
                <tr>
                    @foreach ($teacherQuranGrade->competencyQuran as $competency)
                        <th class="border border-slate-300 text-center">{{ $competency->code }}</th>
                    @endforeach
                </tr>
            @endif
        </thead>
        <tbody>
            @foreach ($students as $student)
                <tr>
                    <td class="border border-slate-300 text-center">{{ $no++ }}</td>
                    <td class="border border-slate-300 px-3 text-left">{{ $student['student']['nis'] }}</td>
                    <td class="border border-slate-300 px-3 text-left">{{ $student['student']['name'] }}</td>
                    @if (count($student['metadata']) > 0)
                        @foreach ($student['metadata'] as $metadata)
                            <td class="border border-slate-300 text-center">
                                {{ $metadata['score'] }}
                            </td>
                        @endforeach

                        <td class="border border-slate-300 text-center">{{ $student['sum'] }}</td>
                        <td
                            class="border border-slate-300 text-center {{ $student['score'] <= 70 ? 'bg-yellow-200' : ($student['score'] >= 95 ? 'bg-red-200' : '') }}">
                            {{ $student['score'] }}
                        </td>
                        <td class="border border-slate-300 text-center">{{ $student['rank'] }}</td>
                    @else
                        <td class="border border-slate-300 text-center">-</td>
                        <td class="border border-slate-300 text-center">-</td>
                        <td class="border border-slate-300 text-center">-</td>
                        <td class="border border-slate-300 text-center">-</td>
                        <td class="border border-slate-300 text-center">-</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>


    <div class="mt-10">
        {{-- buatkan saya title dengan background warna gradasi kuning kemerahan --}}
        <div class="bg-gradient-to-r from-yellow-400 to-red-400 p-6 rounded-lg shadow-lg mb-6">
            <h1 class="text-3xl font-bold text-white text-center mb-2">Data Kompetensi Quran</h1>
        </div>
        @php
            $i = 1;
        @endphp
        {{-- tampilkan kompetensi quran --}}
        <table class="border-collapse border border-slate-400 mt-3" width="100%">
            <thead>
                <tr>
                    <th class="border border-slate-300 text-center">No</th>
                    <th class="border border-slate-300 text-center">Kode</th>
                    <th class="border border-slate-300 text-center">Kompetensi</th>
                    <th class="border border-slate-300 text-center">Kriteria Ketuntasan Minimal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($competencies as $item)
                    <tr>
                        <td class="border border-slate-300 text-center">{{ $i++ }}</td>
                        <td class="border border-slate-300 text-center">{{ $item->code }}</td>
                        <td class="border border-slate-300 text-center">{{ $item->description }}</td>
                        <td class="border border-slate-300 text-center">{{ $item->passing_grade }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td class="border border-slate-300 text-center">-</td>
                    <td class="border border-slate-300 text-center">-</td>
                    <td class="border border-slate-300 text-center">Rata-rata Nilai Minimal</td>
                    <td class="border border-slate-300 text-center">{{ round($competencies->avg('passing_grade'), 0) }}</td>
                </tr>
            </tbody>
        </table>
    </div>


    <div class="mt-10">
        {{-- buatkan saya title dengan background warna gradasi ungu kebiruan --}}
        <div class="bg-gradient-to-r from-purple-400 to-blue-400 p-6 rounded-lg shadow-lg mb-6">
            <h1 class="text-3xl font-bold text-white text-center mb-2">Catatan Guru Quran</h1>
        </div>
        {{-- tampilkan note --}}
        <table class="border-collapse border border-slate-400 mt-3" width="100%">
            <thead>
                <tr>
                    <th class="border border-slate-300 text-center">NIS</th>
                    <th class="border border-slate-300 text-center">Nama</th>
                    <th class="border border-slate-300 text-center">Catatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($studentsWithNotes as $student)
                    <tr>
                        <td class="border border-slate-300 text-center">{{ $student['nis'] }}</td>
                        <td class="border border-slate-300 text-center">{{ $student['name'] }}</td>
                        <td class="border border-slate-300 text-center">{{ $student['note'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
