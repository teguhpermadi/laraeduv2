<x-filament-panels::page>
    <form wire:submit="submit" class="space-y-6">
        {{ $this->form }}

        <div class="flex flex-wrap items-center gap-4 justify-start">
            <x-filament::button type="submit" size="md" wire:loading.attr="disabled" wire:target="submit">
                <span wire:loading.remove wire:target="submit">Import RDM</span>
                <span wire:loading wire:target="submit">Mengimport...</span>
            </x-filament::button>
            <x-filament::button color="gray" tag="a" href="{{ $this->getCancelUrl() }}" size="md">
                Kembali
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
