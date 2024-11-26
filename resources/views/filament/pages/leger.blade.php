<x-filament-panels::page>
    @if($this->hasNoScores)
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4">
            <p class="font-bold">Perhatian!</p>
            <p>Nilai belum diinput untuk beberapa siswa. Silahkan input nilai terlebih dahulu.</p>
        </div>
    @else
        <form wire:submit="submit">
            {{ $this->form }}

            <x-filament::button type="submit" form="submit" class="mt-3">
                Tanda Tangan
            </x-filament::button>

            {{-- buatkan button untuk kembali ke halaman assement --}}
            <x-filament::button tag="a" href="{{ route('filament.admin.pages.assessment.{id}', ['id' => $this->teacherSubject->id]) }}" class="mt-3 ml-3">
                Edit Nilai
            </x-filament::button>

            @if ($this->checkLegerRecap)
                <x-filament::button tag="a" href="{{ route('leger-print', $this->teacherSubject->id) }}" class="mt-3 ml-3" color="success">
                    Cetak Leger
                </x-filament::button>
            @endif
        </form>

        @if($this->checkLegerRecap)
            {{ $this->table }}
        @endif
    @endif
</x-filament-panels::page>
