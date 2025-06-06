<div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <div class="grid grid-cols-2 md:grid-cols-2 gap-1">
        <div class="p-3">
            @livewire(\App\Filament\Resources\TranscriptResource\Widgets\TranscriptDataset1Widget::class)
        </div>
        <div class="p-3">
            @livewire(\App\Filament\Resources\TranscriptResource\Widgets\TranscriptDataset2Widget::class)
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        @foreach ( $students as $student )
        <div class="p-3 md:p-1">
            @livewire('transcript-chart', ['student' => $student], key($student->id))
        </div>
        @endforeach
    </div>
</div>