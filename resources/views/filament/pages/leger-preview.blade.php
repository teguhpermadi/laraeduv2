<h1 class="font-bold dark:text-white">Leger {{ $this->teacherSubject->subject->name }}</h1>
<table class="dark:text-white">
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

<table class="border-collapse border border-slate-400 dark:border-slate-600 mt-3 dark:text-white" width="100%">
    <thead>
        <tr>
            <th rowspan="2" class="border border-slate-300 dark:border-slate-600 text-center bg-gray-100 dark:bg-gray-800">Nomor</th>
            <th rowspan="2" class="border border-slate-300 dark:border-slate-600 text-center bg-gray-100 dark:bg-gray-800">NIS</th>
            <th rowspan="2" class="border border-slate-300 dark:border-slate-600 text-center bg-gray-100 dark:bg-gray-800">Nama Lengkap</th>
            <th colspan="{{ $this->competency_count }}" class="border border-slate-300 dark:border-slate-600 text-center bg-gray-100 dark:bg-gray-800">Nilai Kompetensi</th>
            <th rowspan="2" class="border border-slate-300 dark:border-slate-600 text-center bg-gray-100 dark:bg-gray-800">Jumlah</th>
            <th rowspan="2" class="border border-slate-300 dark:border-slate-600 text-center bg-gray-100 dark:bg-gray-800">Rata-rata</th>
            <th rowspan="2" class="border border-slate-300 dark:border-slate-600 text-center bg-gray-100 dark:bg-gray-800">Peringkat</th>
        </tr>
        @if (count($this->teacherSubject->competency) > 0)
            <tr>
                @foreach ($this->teacherSubject->competency as $competency)
                    <th class="border border-slate-300 dark:border-slate-600 text-center bg-gray-100 dark:bg-gray-800">{{ $competency->code }}</th>
                @endforeach
            </tr>
        @endif
    </thead>
    <tbody>
        @foreach ($this->students as $student)
            <tr>
                <td class="border border-slate-300 dark:border-slate-600 text-center">{{ $no++ }}</td>
                <td class="border border-slate-300 dark:border-slate-600 px-3 text-left">{{ $student['student']['nis'] }}</td>
                <td class="border border-slate-300 dark:border-slate-600 px-3 text-left">{{ $student['student']['name'] }}</td>

                @if (count($student['metadata']) > 0)
                    @foreach ($student['metadata'] as $metadata)
                        <td class="border border-slate-300 dark:border-slate-600 text-center {{ $metadata['score'] <= $metadata->competency->passing_grade || $metadata['score'] >= 95 ? 'bg-gray-300 dark:bg-gray-700' : '' }}">
                            {{ $metadata['score'] }}
                        </td>
                    @endforeach

                    <td class="border border-slate-300 dark:border-slate-600 text-center">{{ $student['sum'] }}</td>
                    <td class="border border-slate-300 dark:border-slate-600 text-center {{ $student['avg'] <= $this->teacherSubject->passing_grade || $student['avg'] >= 95 ? 'bg-gray-300 dark:bg-gray-700' : '' }}">
                        {{ $student['avg'] }}
                    </td>
                    <td class="border border-slate-300 dark:border-slate-600 text-center">{{ $student['rank'] }}</td>
                @else
                    <td class="border border-slate-300 dark:border-slate-600 text-center">-</td>
                    <td class="border border-slate-300 dark:border-slate-600 text-center">-</td>
                    <td class="border border-slate-300 dark:border-slate-600 text-center">-</td>
                    <td class="border border-slate-300 dark:border-slate-600 text-center">-</td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>
