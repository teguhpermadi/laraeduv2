<h1 class="font-bold dark:text-white">Leger {{ $this->teacherSubject->subject->name }}</h1>
<table class="dark:text-white">
    <tr>
        <td>Tahun Pelajaran</td>
        <td>:</td>
        <td>{{ $teacherSubject->academic->year }}</td>
    </tr>
    <tr>
        <td>Semester</td>
        <td>:</td>
        <td>{{ $teacherSubject->academic->semester }}</td>
    </tr>
    <tr>
        <td>Kelas</td>
        <td>:</td>
        <td>{{ $teacherSubject->grade->name }}</td>
    </tr>
    <tr>
        <td>Guru</td>
        <td>:</td>
        <td>{{ $teacherSubject->teacher->name }}</td>
    </tr>
</table>

@php
    $no = 1;
@endphp

<table class="border-collapse border border-slate-400 dark:border-slate-600 mt-3 dark:text-white" width="100%">
    <thead>
        <tr>
            <th class="border border-slate-300 dark:border-slate-600 text-center bg-gray-100 dark:bg-gray-800">Nomor</th>
            <th class="border border-slate-300 dark:border-slate-600 text-center bg-gray-100 dark:bg-gray-800">NIS</th>
            <th class="border border-slate-300 dark:border-slate-600 text-center bg-gray-100 dark:bg-gray-800">Nama
                Lengkap</th>
            @foreach ($competencies as $competency)
                <th class="border border-slate-300 dark:border-slate-600 text-center bg-gray-100 dark:bg-gray-800">
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
            <th class="border border-slate-300 dark:border-slate-600 text-center bg-gray-100 dark:bg-gray-800">Jumlah
            </th>
            <th class="border border-slate-300 dark:border-slate-600 text-center bg-gray-100 dark:bg-gray-800">Rata-rata
            </th>
            <th class="border border-slate-300 dark:border-slate-600 text-center bg-gray-100 dark:bg-gray-800">Peringkat
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($students as $student)
            <tr>
                <td class="border border-slate-300 dark:border-slate-600 text-center">{{ $no++ }}</td>
                <td class="border border-slate-300 dark:border-slate-600 px-3 text-left">{{ $student['nis'] }}</td>
                <td class="border border-slate-300 dark:border-slate-600 px-3 text-left">{{ $student['name'] }}</td>

                @if (count($student['competencies']) > 0)
                    @foreach ($student['competencies'] as $competency)
                        <td
                            class="border border-slate-300 dark:border-slate-600 text-center {{ $competency['score'] <= $competency['passing_grade'] || $competency['score'] >= 95 ? 'bg-gray-300 dark:bg-gray-700' : '' }}">
                            {{ $competency['score'] }}
                        </td>
                    @endforeach

                    <td class="border border-slate-300 dark:border-slate-600 text-center">{{ $student['sum_score'] }}
                    </td>
                    <td
                        class="border border-slate-300 dark:border-slate-600 text-center {{ $student['avg_score'] <= $this->teacherSubject->passing_grade || $student['avg_score'] >= 95 ? 'bg-gray-300 dark:bg-gray-700' : '' }}">
                        {{ $student['avg_score'] }}
                    </td>
                    <td class="border border-slate-300 dark:border-slate-600 text-center">{{ $student['ranking'] }}
                    </td>
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
