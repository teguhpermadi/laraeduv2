<x-filament-panels::page>
    {{-- {{$this->form}} --}}
    <form wire:submit="submit">
        {{ $this->form }}
        
        {{-- <x-filament::button >
            Submit
        </x-filament::button> --}}
    </form>
    {{$this->table}}
</x-filament-panels::page>
