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

<hr>

@php
    $no = 1;
@endphp

<table>
    <thead>
        <tr>
            <th rowspan="2">Nomor</th>
            <th rowspan="2">NIS</th>
            <th rowspan="2">Nama Lengkap</th>
            <th colspan="{{ $this->teacherSubject->competency_count }}">Nilai Kompetensi</th>
            <th rowspan="2">Jumlah</th>
            <th rowspan="2">Rata-rata</th>
        </tr>
        @if (count($this->teacherSubject->competency)>0)
        {{-- jika kompetensi ada --}}
        <tr>
            @foreach ($this->teacherSubject->competency as $competency)
            <th>{{ $competency->code }}</th>
            @endforeach
        </tr>
        @endif
    </thead>
    <tbody>
        @foreach ($this->students as $student)
            <tr>
                <td>{{ $no++ }}</td>
                <td>{{ $student['student']['nis'] }}</td>
                <td>{{ $student['student']['name'] }}</td>

                @if (count($student['metadata'])>0)
                    {{-- jika kompetensi ada --}}
                    @foreach ($student['metadata'] as $metadata)
                        <td>{{$metadata['score']}}</td>
                    @endforeach
                    <td>{{$student['sum']}}</td>
                    <td>{{$student['avg']}}</td>
                @else
                    {{-- jika kompetensi tidak ada --}}
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>
