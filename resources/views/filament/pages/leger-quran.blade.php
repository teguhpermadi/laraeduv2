<x-filament-panels::page>
    <form wire:submit="submit">
        {{ $this->form }}

        <x-filament::button type="submit" class="mt-3" form="submit">
            Tanda Tangan
        </x-filament::button>

        {{-- buatkan button untuk kembali ke halaman assement --}}
        <x-filament::button tag="a" href="{{ route('filament.admin.pages.assessment-quran') }}" class="mt-3 ml-3">
            Edit Nilai
        </x-filament::button>

        @if ($this->checkLegerQuran)
            <x-filament::button tag="a" href="{{ route('leger-quran-print', $this->teacherQuran->id) }}" class="mt-3 ml-3">
                Cetak Leger
            </x-filament::button>
        @endif
    </form>
</x-filament-panels::page>
