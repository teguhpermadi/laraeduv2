<div>
    {{-- title half semester --}}
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-6 rounded-lg shadow-lg mb-6">
        <h1 class="text-3xl font-bold text-white text-center mb-2">Leger Tengah Semester</h1>
        <div class="bg-white/10 p-4 rounded-md">
            <table class="text-white w-full max-w-2xl mx-auto">
                <tr>
                    <td class="py-1 font-semibold">Tahun Pelajaran</td>
                    <td class="px-3">:</td>
                    <td>{{ $teacherSubject->academic->year }}</td>
                </tr>
                <tr>
                    <td class="py-1 font-semibold">Semester</td>
                    <td class="px-3">:</td>
                    <td>{{ $teacherSubject->academic->semester }}</td>
                </tr>
                <tr>
                    <td class="py-1 font-semibold">Kelas</td>
                    <td class="px-3">:</td>
                    <td>{{ $teacherSubject->grade->name }}</td>
                </tr>
                <tr>
                    <td class="py-1 font-semibold">Guru</td>
                    <td class="px-3">:</td>
                    <td>{{ $teacherSubject->teacher->name }}</td>
                </tr>
                <tr>
                    <td class="py-1 font-semibold">Mata Pelajaran</td>
                    <td class="px-3">:</td>
                    <td>{{ $teacherSubject->subject->name }}</td>
                </tr>
                {{-- tampilkan tanggal cetak --}}
                <tr>
                    <td class="py-1 font-semibold">Tanggal Cetak</td>
                    <td class="px-3">:</td>
                    {{-- buat format tanggal menjadi 30 November 2024 --}}
                    <td>{{ $legerRecapHalfSemester->created_at->translatedFormat('l, d F Y H:i') }}</td>
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
                <th colspan="{{ $competencyCountHalfSemester }}" class="border border-slate-300 text-center">Nilai
                    Kompetensi</th>
                <th rowspan="2" class="border border-slate-300 text-center">Jumlah</th>
                <th rowspan="2" class="border border-slate-300 text-center">Rata-rata</th>
                <th rowspan="2" class="border border-slate-300 text-center">Peringkat</th>
            </tr>
            @if ($competencyCountHalfSemester > 0)
                <tr>
                    @foreach ($competencyHalfSemester as $competency)
                        <th class="border border-slate-300 text-center">
                            @switch($competency->code)
                                @case(App\Enums\CategoryLegerEnum::FULL_SEMESTER->value)
                                    {{ App\Enums\CategoryLegerEnum::FULL_SEMESTER->getLabel() }}
                                @break

                                @case(App\Enums\CategoryLegerEnum::HALF_SEMESTER->value)
                                    {{ App\Enums\CategoryLegerEnum::HALF_SEMESTER->getLabel() }}
                                @break

                                @default
                                    {{ $competency->code }}
                            @endswitch
                        </th>
                    @endforeach
                </tr>
            @endif
        </thead>
        <tbody>
            @foreach ($legerHalfSemester as $student)
                <tr>
                    {{-- {{ dd($student) }} --}}
                    <td class="border border-slate-300 text-center">{{ $no++ }}</td>
                    <td class="border border-slate-300 px-3 text-left">{{ $student['student']['nis'] }}</td>
                    <td class="border border-slate-300 px-3 text-left">{{ $student['student']['name'] }}</td>


                    @if (count($student['metadata']) > 0)
                        @foreach ($student['metadata'] as $metadata)
                            <td
                                class="border border-slate-300 text-center {{ $metadata['score'] <= $metadata['passing_grade'] ? 'bg-yellow-200' : ($metadata['score'] >= 95 ? 'bg-red-200' : '') }}">
                                {{ $metadata['score'] }}
                            </td>
                        @endforeach

                        <td class="border border-slate-300 text-center">
                            {{ $student['sum'] }}
                        </td>
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
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-10">
        {{-- buatkan saya title dengan background warna gradasi kuning kemerahan --}}
        <div class="bg-gradient-to-r from-yellow-400 to-red-400 p-6 rounded-lg shadow-lg mb-6">
            <h1 class="text-3xl font-bold text-white text-center mb-2">Data Kompetensi</h1>
        </div>
        {{-- tampilkan data competency --}}
        <table class="border-collapse border border-slate-400 mt-3" width="100%">
            <thead>
                <tr>
                    <th class="border border-slate-300 text-center">Kode</th>
                    <th class="border border-slate-300 text-center">Kompetensi</th>
                    <th class="border border-slate-300 text-center">Nilai Minimal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($teacherSubject->competency->where('half_semester', 1) as $competency)
                    <tr>
                        <td class="border border-slate-300 text-center">{{ $competency->code }}</td>
                        <td class="border border-slate-300 text-center">{{ $competency->description }}</td>
                        <td class="border border-slate-300 text-center">{{ $competency->passing_grade }}</td>
                    </tr>
                @endforeach
                {{-- rata rata passing grade --}}
                <tr>
                    <td class="border border-slate-300 text-center">-</td>
                    <td class="border border-slate-300 text-center">Rata-rata Nilai Minimal</td>
                    {{-- bulatkan rata-rata passing grade --}}
                    <td class="border border-slate-300 text-center">{{ round($teacherSubject->competency->where('half_semester', 1)->avg('passing_grade'), 0) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="mt-10">
        {{-- buatkan saya title dengan background warna gradasi ungu kebiruan --}}
        <div class="bg-gradient-to-r from-purple-400 to-blue-400 p-6 rounded-lg shadow-lg mb-6">
            <h1 class="text-3xl font-bold text-white text-center mb-2">Catatan Guru</h1>
        </div>
        {{-- tampilkan note half semester --}}
        <div class="bg-white/10 p-4 rounded-md">
            <table class="text-black w-full max-w-2xl mx-auto">
                <thead>
                    <tr>
                        <th class="border border-slate-300 text-center">No</th>
                        <th class="border border-slate-300 text-center">NIS</th>
                        <th class="border border-slate-300 text-center">Nama Siswa</th>
                        <th class="border border-slate-300 text-center">Catatan Guru</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $j = 1;
                    @endphp
                    @foreach ($studentsWithNotesHalfSemester as $studentHalfSemester)
                        <tr>
                            <td class="border border-slate-300 text-center">{{ $j++ }}</td>
                            <td class="border border-slate-300 text-center">{{ $studentHalfSemester['nis'] }}</td>
                            <td class="border border-slate-300 text-center">{{ $studentHalfSemester['name'] }}</td>
                            <td class="border border-slate-300 text-center">{{ $studentHalfSemester['note'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- buatkan page break dengan sebuah garis horizontal --}}
    <div style="page-break-after: always;"></div>
    {{-- end page break --}}

    {{-- title full semester --}}
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-6 rounded-lg shadow-lg mb-6">
        <h1 class="text-3xl font-bold text-white text-center mb-2">Leger Akhir Semester</h1>
        <div class="bg-white/10 p-4 rounded-md">
            <table class="text-white w-full max-w-2xl mx-auto">
                <tr>
                    <td class="py-1 font-semibold">Tahun Pelajaran</td>
                    <td class="px-3">:</td>
                    <td>{{ $teacherSubject->academic->year }}</td>
                </tr>
                <tr>
                    <td class="py-1 font-semibold">Semester</td>
                    <td class="px-3">:</td>
                    <td>{{ $teacherSubject->academic->semester }}</td>
                </tr>
                <tr>
                    <td class="py-1 font-semibold">Kelas</td>
                    <td class="px-3">:</td>
                    <td>{{ $teacherSubject->grade->name }}</td>
                </tr>
                <tr>
                    <td class="py-1 font-semibold">Guru</td>
                    <td class="px-3">:</td>
                    <td>{{ $teacherSubject->teacher->name }}</td>
                </tr>
                <tr>
                    <td class="py-1 font-semibold">Mata Pelajaran</td>
                    <td class="px-3">:</td>
                    <td>{{ $teacherSubject->subject->name }}</td>
                </tr>
                {{-- tampilkan tanggal cetak --}}
                <tr>
                    <td class="py-1 font-semibold">Tanggal Cetak</td>
                    <td class="px-3">:</td>
                    {{-- buat format tanggal menjadi 30 November 2024 --}}
                    <td>{{ $legerRecapFullSemester->created_at->translatedFormat('l, d F Y H:i') }}</td>
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
                <th colspan="{{ $competencyCountFullSemester }}" class="border border-slate-300 text-center">Nilai
                    Kompetensi</th>
                <th rowspan="2" class="border border-slate-300 text-center">Jumlah</th>
                <th rowspan="2" class="border border-slate-300 text-center">Rata-rata</th>
                <th rowspan="2" class="border border-slate-300 text-center">Peringkat</th>
            </tr>
            @if ($competencyCountFullSemester > 0)
                <tr>
                    @foreach ($competencyFullSemester as $competency)
                        <th class="border border-slate-300 text-center">
                            @switch($competency->code)
                                @case(App\Enums\CategoryLegerEnum::FULL_SEMESTER->value)
                                    {{ App\Enums\CategoryLegerEnum::FULL_SEMESTER->getLabel() }}
                                @break

                                @case(App\Enums\CategoryLegerEnum::HALF_SEMESTER->value)
                                    {{ App\Enums\CategoryLegerEnum::HALF_SEMESTER->getLabel() }}
                                @break

                                @default
                                    {{ $competency->code }}
                            @endswitch
                        </th>
                    @endforeach
                </tr>
            @endif
        </thead>
        <tbody>
            @foreach ($legerFullSemester as $student)
                <tr>
                    <td class="border border-slate-300 text-center">{{ $no++ }}</td>
                    <td class="border border-slate-300 px-3 text-left">{{ $student['student']['nis'] }}</td>
                    <td class="border border-slate-300 px-3 text-left">{{ $student['student']['name'] }}</td>


                    @if (count($student['metadata']) > 0)
                        @foreach ($student['metadata'] as $metadata)
                            <td
                                class="border border-slate-300 text-center {{ $metadata['score'] <= $metadata['passing_grade'] ? 'bg-yellow-200' : ($metadata['score'] >= 95 ? 'bg-red-200' : '') }}">
                                {{ $metadata['score'] }}
                            </td>
                        @endforeach

                        <td class="border border-slate-300 text-center">

                            {{ $student['sum'] }}
                        </td>
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
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-10">
        {{-- buatkan saya title dengan background warna gradasi kuning kemerahan --}}
        <div class="bg-gradient-to-r from-yellow-400 to-red-400 p-6 rounded-lg shadow-lg mb-6">
            <h1 class="text-3xl font-bold text-white text-center mb-2">Data Kompetensi</h1>
        </div>
        {{-- tampilkan data competency --}}
        <table class="border-collapse border border-slate-400 mt-3" width="100%">
            <thead>
                <tr>
                    <th class="border border-slate-300 text-center">Kode</th>
                    <th class="border border-slate-300 text-center">Kompetensi</th>
                    <th class="border border-slate-300 text-center">Nilai Minimal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($teacherSubject->competency as $competency)
                    <tr>
                        <td class="border border-slate-300 text-center">{{ $competency->code }}</td>
                        <td class="border border-slate-300 text-center">{{ $competency->description }}</td>
                        <td class="border border-slate-300 text-center">{{ $competency->passing_grade }}</td>
                    </tr>
                @endforeach
                {{-- rata rata passing grade --}}
                <tr>
                    <td class="border border-slate-300 text-center">-</td>
                    <td class="border border-slate-300 text-center">Rata-rata Nilai Minimal</td>
                    {{-- bulatkan rata-rata passing grade --}}
                    <td class="border border-slate-300 text-center">{{ round($teacherSubject->competency->avg('passing_grade'), 0) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="mt-10">
        {{-- buatkan saya title dengan background warna gradasi ungu kebiruan --}}
        <div class="bg-gradient-to-r from-purple-400 to-blue-400 p-6 rounded-lg shadow-lg mb-6">
            <h1 class="text-3xl font-bold text-white text-center mb-2">Catatan Guru</h1>
        </div>
        {{-- tampilkan note full semester --}}
        <div class="bg-white/10 p-4 rounded-md">
            <table class="text-black w-full max-w-2xl mx-auto">
                <thead>
                    <tr>
                        <th class="border border-slate-300 text-center">No</th>
                        <th class="border border-slate-300 text-center">NIS</th>
                        <th class="border border-slate-300 text-center">Nama Siswa</th>
                        <th class="border border-slate-300 text-center">Catatan Guru</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $k = 1;
                    @endphp
                    @foreach ($studentsWithNotesFullSemester as $studentFullSemester)
                        <tr>
                            <td class="border border-slate-300 text-center">{{ $k++ }}</td>
                            <td class="border border-slate-300 text-center">{{ $studentFullSemester['nis'] }}</td>
                            <td class="border border-slate-300 text-center">{{ $studentFullSemester['name'] }}</td>
                            <td class="border border-slate-300 text-center">{{ $studentFullSemester['note'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
