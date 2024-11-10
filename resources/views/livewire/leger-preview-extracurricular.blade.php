<div>
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 p-6 rounded-lg shadow-lg mb-6">
        <h1 class="text-3xl font-bold text-white text-center mb-2">Leger Ekstrakurikuler
            {{ $extracurricular->extracurricular->name }}</h1>
        <div class="bg-white/10 p-4 rounded-md">
            <table class="text-white w-full max-w-2xl mx-auto">
                <tr>
                    <td class="py-1 font-semibold">Guru Pembimbing</td>
                    <td class="px-3">:</td>
                    <td>{{ $extracurricular->teacher->name }}</td>
                </tr>
                <tr>
                    <td class="py-1 font-semibold">Tanggal Cetak</td>
                    <td class="px-3">:</td>
                    <td>{{ now()->format('d F Y') }} Pukul {{ now()->format('H:i') }}</td>
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
                <th class="border border-slate-300 text-center bg-gray-100">Nomor</th>
                <th class="border border-slate-300 text-center bg-gray-100">NISN</th>
                <th class="border border-slate-300 text-center bg-gray-100">Nama Siswa</th>
                <th class="border border-slate-300 text-center bg-gray-100">Nilai</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($students as $student)
                <tr>
                    <td class="border border-slate-300 text-center">{{ $no++ }}</td>
                    <td class="border border-slate-300 px-3 text-left">{{ $student->student->nisn }}</td>
                    <td class="border border-slate-300 px-3 text-left">{{ $student->student->name }}</td>
                    <td class="border border-slate-300 text-center">{{ $student->score }} </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- keterangan score berdasarkan linkertscaleenum --}}
    <div class="mt-3">
        <p class="text-sm text-gray-500">Keterangan: 4 = Amat Baik, 3 = Baik, 2 = Cukup, 1 = Kurang, 0 = Amat Kurang</p>
    </div>
</div>
