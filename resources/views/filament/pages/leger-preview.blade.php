<h1 class="font-bold">Leger {{ $this->teacherSubject->subject->name }}</h1>
<table>
    <tr>
        <td>Tahun Pelajaran</td>
        <td>:</td>
        <td>{{ $this->teacherSubject->academic->year }}</td>
    </tr>
    <tr>
        <td>Semester</td>
        <td>:</td>
        <td>{{ $this->teacherSubject->academic->semester }}</td>
    </tr>
    <tr>
        <td>Kelas</td>
        <td>:</td>
        <td>{{ $this->teacherSubject->grade->name }}</td>
    </tr>
    <tr>
        <td>Guru</td>
        <td>:</td>
        <td>{{ $this->teacherSubject->teacher->name }}</td>
    </tr>
</table>

@php
    $no = 1;
@endphp

<table class="border-collapse border border-slate-400 mt-3" width="100%">
    <thead>
        <tr>
            <th rowspan="2" class="border border-slate-300 text-center">Nomor</th>
            <th rowspan="2" class="border border-slate-300 text-center">NIS</th>
            <th rowspan="2" class="border border-slate-300 text-center">Nama Lengkap</th>
            {{-- <th colspan="{{ $this->teacherSubject->competency_count }}" class="border border-slate-300 text-center">Nilai --}}
            <th colspan="{{ $this->competency_count }}" class="border border-slate-300 text-center">Nilai
                Kompetensi</th>
            <th rowspan="2" class="border border-slate-300 text-center">Jumlah</th>
            <th rowspan="2" class="border border-slate-300 text-center">Rata-rata</th>
            <th rowspan="2" class="border border-slate-300 text-center">Peringkat</th>
        </tr>
        @if (count($this->teacherSubject->competency) > 0)
            {{-- jika kompetensi ada --}}
            <tr>
                @foreach ($this->teacherSubject->competency as $competency)
                    <th class="border border-slate-300 text-center">{{ $competency->code }}</th>
                @endforeach
            </tr>
        @endif
    </thead>
    <tbody>
        @foreach ($this->students as $student)
            <tr>
                <td class="border border-slate-300 text-center">{{ $no++ }}</td>
                <td class="border border-slate-300 px-3 text-left">{{ $student['student']['nis'] }}</td>
                <td class="border border-slate-300 px-3 text-left">{{ $student['student']['name'] }}</td>

                @if (count($student['metadata']) > 0)
                    {{-- jika kompetensi ada --}}
                    @foreach ($student['metadata'] as $metadata)
                        <td
                            class="border border-slate-300 text-center {{ $metadata['score'] <= $metadata->competency->passing_grade || $metadata['score'] >= 95 ? 'bg-gray-300' : '' }}">
                            {{ $metadata['score'] }}
                        </td>
                    @endforeach

                    <td class="border border-slate-300 text-center">{{ $student['sum'] }}</td>
                    <td
                        class="border border-slate-300 text-center {{ $student['avg'] <= 70 || $student['avg'] >= 95 ? 'bg-gray-300' : '' }}">
                        {{ $student['avg'] }}</td>
                    <td class="border border-slate-300 text-center">{{ $student['rank'] }}</td>
                @else
                    {{-- jika kompetensi tidak ada --}}
                    <td class="border border-slate-300 text-center">-</td>
                    <td class="border border-slate-300 text-center">-</td>
                    <td class="border border-slate-300 text-center">-</td>
                    <td class="border border-slate-300 text-center">-</td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>