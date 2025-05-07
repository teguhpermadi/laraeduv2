<div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@foreach ( $students as $student )
    @livewire('transcript-chart', ['student' => $student], key($student->id))
@endforeach
</div>
