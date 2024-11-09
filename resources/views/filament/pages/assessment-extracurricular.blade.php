<x-filament-panels::page>
    <form wire:submit="submit">
        {{ $this->form }}
    </form>
    {{ $this->table }}
</x-filament-panels::page>
