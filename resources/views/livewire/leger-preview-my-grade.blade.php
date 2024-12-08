<div>
    {{-- Knowing others is intelligence; knowing yourself is true wisdom. --}}
    @foreach ($myGrade as $grade)
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-6 rounded-lg shadow-lg mb-6">
            <h1 class="text-3xl font-bold text-white text-center mb-2">Leger Kelas</h1>
            <div class="bg-white/10 p-4 rounded-md">
                <table class="text-white w-full max-w-2xl mx-auto">
                    <tr>
                        <td class="py-1 font-semibold">Tahun Pelajaran</td>
                        <td class="px-3">:</td>
                        <td>{{ $grade->academic->year }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 font-semibold">Semester</td>
                        <td class="px-3">:</td>
                        <td>{{ $grade->academic->semester }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 font-semibold">Kelas</td>
                        <td class="px-3">:</td>
                        <td>{{ $grade->grade->name }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 font-semibold">Guru</td>
                        <td class="px-3">:</td>
                        <td>{{ $grade->teacher->name }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- berikan div untuk container --}}
        <div class="bg-white/10 p-4 rounded-md">
            {{-- buatkan judul tabel --}}
            <h1 class="text-2xl font-bold text-black text-center mb-2">Data Nilai</h1>

            <table class="text-black w-full border-collapse border border-gray-300">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="border border-gray-300 p-3 text-center">No</th>
                        <th class="border border-gray-300 p-3 text-center">NIS</th>
                        <th class="border border-gray-300 p-3 text-left">Nama</th>
                        @foreach ($grade->grade->teacherSubject as $subject)
                            <th class="border border-gray-300 p-3 text-center">{{ $subject->subject->code }}</th>
                        @endforeach
                        <th class="border border-gray-300 p-3">Jumlah Nilai</th>
                        <th class="border border-gray-300 p-3">Rata-rata</th>
                        <th class="border border-gray-300 p-3">Rangking</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data[$grade->id] as $student)
                        <tr class="border odd:bg-gray-100">
                            <td class="p-3 text-center">{{ $student['no'] }}</td>
                            <td class="p-3 text-left">{{ $student['student']['nis'] }}</td>
                            <td class="p-3 text-left">{{ $student['student']['name'] }}</td>
                            @if (isset($student['leger']))
                                @foreach ($student['leger'] as $leger)
                                    @if ($leger)
                                        <td class="p-3 text-center">{{ $leger['score'] }}</td>
                                    @else
                                        <td class="p-3 text-center">-</td>
                                    @endif
                                @endforeach
                            @else
                                {{-- tambahkan kolom kosong berdasarkan jumlah subject --}}
                                @for ($i = 0; $i < count($grade->grade->teacherSubject); $i++)
                                    <td class="p-3 text-center">-</td>
                                @endfor
                            @endif
                            <td class="p-3 text-center">{{ $student['total'] }}</td>
                            <td class="p-3 text-center">{{ $student['average'] }}</td>
                            <td class="p-3 text-center">{{ $student['ranking'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <br>

        <div class="bg-white/10 p-4 rounded-md">
            {{-- buatkan judul tabel --}}
            <h1 class="text-2xl font-bold text-black text-center mb-2">Data Kehadiran</h1>

            {{-- buatkan tabel untuk data attendance --}}
            <table class="text-black w-full border-collapse border border-gray-300">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="border border-gray-300 p-3 text-center">No</th>
                        <th class="border border-gray-300 p-3 text-center">NIS</th>
                        <th class="border border-gray-300 p-3 text-left">Nama</th>
                        <th class="border border-gray-300 p-3 text-center">Status</th>
                        <th class="border border-gray-300 p-3 text-center">Sakit</th>
                        <th class="border border-gray-300 p-3 text-center">Izin</th>
                        <th class="border border-gray-300 p-3 text-center">Alpa</th>
                        <th class="border border-gray-300 p-3 text-center">Catatan</th>
                        <th class="border border-gray-300 p-3 text-center">Prestasi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data[$grade->id] as $student)
                        <tr class="border odd:bg-gray-100">
                            <td class="p-3 text-center">{{ $student['no'] }}</td>
                            <td class="p-3 text-left">{{ $student['student']['nis'] }}</td>
                            <td class="p-3 text-left">{{ $student['student']['name'] }}</td>
                            @if (isset($student['attendance']))
                                <td class="p-3 text-center">
                                    {{-- jika true maka naik kelas, jika false maka tidak naik kelas --}}
                                    @if ($student['attendance']['status'])
                                        <span class="text-green-500">Naik Kelas</span>
                                    @else
                                        <span class="text-red-500">Tidak Naik Kelas</span>
                                    @endif
                                </td>
                                <td class="p-3 text-center">{{ $student['attendance']['sick'] }}</td>
                                <td class="p-3 text-center">{{ $student['attendance']['permission'] }}</td>
                                <td class="p-3 text-center">{{ $student['attendance']['absent'] }}</td>
                                <td class="p-3 text-left">{{ $student['attendance']['note'] }}</td>
                                <td class="p-3 text-left">{{ $student['attendance']['achievement'] }}</td>
                            @else
                                <td class="p-3 text-center">-</td>
                                <td class="p-3 text-center">-</td>
                                <td class="p-3 text-center">-</td>
                                <td class="p-3 text-center">-</td>
                                <td class="p-3 text-center">-</td>
                                <td class="p-3 text-center">-</td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- berikan page break --}}
        <div style="page-break-after: always;"></div>
    @endforeach
</div>
