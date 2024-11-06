<x-filament-panels::page>
    <form wire:submit="submit" class="mb-4">
        {{$this->form}}
        {{-- <x-filament::button class="mt-3" type="submit" wire:loading.class="opacity-50">
            Check
        </x-filament::button> --}}
    </form>

    {{$this->table}}
</x-filament-panels::page>
