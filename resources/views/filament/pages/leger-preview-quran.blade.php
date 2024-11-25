<div>
    <!-- The only way to do great work is to love what you do. - Steve Jobs -->
    <h1 class="font-bold dark:text-white">Leger Quran</h1>
    <table class="dark:text-white">
        <tr>
            <td>Tahun Pelajaran</td>
            <td>:</td>
            <td>{{ $this->teacherQuranGrade->academicYear->year }}</td>
        </tr>
        <tr>
            <td>Semester</td>
            <td>:</td>
            <td>{{ $this->teacherQuranGrade->academicYear->semester }}</td>
        </tr>
        <tr>
            <td>Kelas Quran</td>
            <td>:</td>
            <td>{{ $this->teacherQuranGrade->quranGrade->name }}</td>
        </tr>
        <tr>
            <td>Guru</td>
            <td>:</td>
            <td>{{ $this->teacherQuranGrade->teacher->name }}</td>
        </tr>
    </table>

    @php
        $no = 1;
    @endphp

    <table class="border-collapse border border-slate-400 dark:border-slate-600 mt-3 dark:text-white" width="100%">
        <thead>
            <tr>
                <th rowspan="2"
                    class="border border-slate-300 dark:border-slate-600 text-center bg-gray-100 dark:bg-gray-800">Nomor
                </th>
                <th rowspan="2"
                    class="border border-slate-300 dark:border-slate-600 text-center bg-gray-100 dark:bg-gray-800">NIS
                </th>
                <th rowspan="2"
                    class="border border-slate-300 dark:border-slate-600 text-center bg-gray-100 dark:bg-gray-800">Nama
                    Lengkap</th>
                <th colspan="{{ $this->competencyQuranCount }}"
                    class="border border-slate-300 dark:border-slate-600 text-center bg-gray-100 dark:bg-gray-800">Nilai
                    Kompetensi</th>
                <th rowspan="2"
                    class="border border-slate-300 dark:border-slate-600 text-center bg-gray-100 dark:bg-gray-800">
                    Jumlah</th>
                <th rowspan="2"
                    class="border border-slate-300 dark:border-slate-600 text-center bg-gray-100 dark:bg-gray-800">
                    Rata-rata</th>
                <th rowspan="2"
                    class="border border-slate-300 dark:border-slate-600 text-center bg-gray-100 dark:bg-gray-800">
                    Peringkat</th>
            </tr>
            @if (count($this->teacherQuranGrade->competencyQuran) > 0)
                <tr>
                    @foreach ($this->teacherQuranGrade->competencyQuran as $competency)
                        <th
                            class="border border-slate-300 dark:border-slate-600 text-center bg-gray-100 dark:bg-gray-800">
                            {{ $competency->code }}
                        </th>
                    @endforeach
                </tr>
            @endif
        </thead>
        <tbody>
            @foreach ($this->students as $student)
                <tr>
                    <td class="border border-slate-300 dark:border-slate-600 text-center">{{ $no++ }}</td>
                    <td class="border border-slate-300 dark:border-slate-600 px-3 text-left">{{ $student['nis'] }}</td>
                    <td class="border border-slate-300 dark:border-slate-600 px-3 text-left">{{ $student['name'] }}</td>
                    {{-- foreach competency --}}
                    @foreach ($student['competencies'] as $competency)
                        <td class="border border-slate-300 dark:border-slate-600 text-center">{{ $competency['score'] }}</td>
                    @endforeach
                    <td class="border border-slate-300 dark:border-slate-600 text-center">{{ $student['sum_score'] }}</td>
                    <td class="border border-slate-300 dark:border-slate-600 text-center">{{ $student['avg_score'] }}</td>
                    <td class="border border-slate-300 dark:border-slate-600 text-center">{{ $student['ranking'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
