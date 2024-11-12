<x-filament-panels::page>
    <form wire:submit="submit">
        {{ $this->form }}

        <x-filament::button type="submit" class="mt-3">
            Tanda Tangan
        </x-filament::button>

        {{-- @if ($this->checkLegerRecap)
            <x-filament::button tag="a" href="{{ route('leger-print', $this->teacherSubject->id) }}" class="mt-3 ml-3">
                Cetak Leger
            </x-filament::button>
        @endif --}}
    </form>
</x-filament-panels::page>
