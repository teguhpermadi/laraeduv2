<table>
    <thead>
        <tr>
            <th>No</th>
            <th>NISN</th>
            <th>Nama Siswa</th>
            @foreach ($subjects as $subject)
                <th>{{ $subject->subject->code }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($students as $i => $student)
        @php
            $transcripts = $student->transcript->sortBy('subject_id');
        @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $student->nisn }}</td>
                <td>{{ $student->name }}</td>
                @foreach ($transcripts as $transcript)
                    <td>{{ $transcript->average_score }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>