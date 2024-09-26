<x-filament-widgets::widget>
    <x-filament::section>
        <form wire:submit="submit"> 
            {{ $this->form }}
 
            <x-filament::button type="submit" class="mt-3">
                {{ __('Save') }}
            </x-filament::button>
        </form> 
    </x-filament::section>
</x-filament-widgets::widget>
