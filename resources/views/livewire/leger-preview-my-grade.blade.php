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

        
        <table class="text-black w-full max-w-2xl mx-auto border-collapse border border-gray-300">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border border-gray-300 px-4 py-2">No</th>
                    <th class="border border-gray-300 px-4 py-2">Nama</th>
                    @foreach ($grade->grade->teacherSubject as $subject)
                        <th class="border border-gray-300 px-4 py-2">{{ $subject->subject->code }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php
                    $no = 1;
                @endphp
                @foreach ($grade->studentGrade as $student)
                    <tr class="{{ $loop->even ? 'bg-gray-100' : 'bg-white' }}">
                        <td class="border border-gray-300 px-4 py-2">{{ $no++ }}</td>
                        <td class="border border-gray-300 px-4 py-2">{{ $student->student->name }}</td>
                        @foreach ($grade->grade->teacherSubject as $subject)
                            @php
                                $leger = $student->student
                                    ->leger()
                                    ->where('category', 'full_semester')
                                    ->where('subject_id', $subject->subject_id)
                                    ->first();
                            @endphp

                            @if ($leger)
                                <td class="border border-gray-300 px-4 py-2">{{ $leger->score }}</td>
                            @else
                                <td class="border border-gray-300 px-4 py-2">-</td>
                            @endif

                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
</div>
