<div>
    {{-- {{ $teacherSubjects }} --}}
    {{-- jika teacherSubjects tidak null maka tampilkan livewire else tampilkan text --}}
    @if($teacherSubjects)
        @livewire('competency-reference', ['teacherSubjects' => $teacherSubjects])
    @else
        <p>Anda belum memilih kelas dan mata pelajaran</p>
    @endif
</div>
