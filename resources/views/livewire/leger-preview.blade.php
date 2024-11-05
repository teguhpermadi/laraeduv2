<div>
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-6 rounded-lg shadow-lg mb-6">
        <h1 class="text-3xl font-bold text-white text-center mb-2">Leger {{ $teacherSubject->subject->name }}</h1>
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
            @if (count($teacherSubject->competency) > 0)
                <tr>
                    @foreach ($teacherSubject->competency as $competency)
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
                            <td
                                class="border border-slate-300 text-center {{ $metadata['score'] <= $metadata['competency']['passing_grade'] ? 'bg-yellow-200' : ($metadata['score'] >= 95 ? 'bg-red-200' : '') }}">
                                {{ $metadata['score'] }}
                            </td>
                        @endforeach
                        
                        <td class="border border-slate-300 text-center">{{ $student['sum'] }}</td>
                        <td
                            class="border border-slate-300 text-center {{ $student['avg'] <= 70 ? 'bg-yellow-200' : ($student['avg'] >= 95 ? 'bg-red-200' : '') }}">
                            {{ $student['avg'] }}
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
</div>
