<x-filament-panels::page>
    <x-filament-panels::form>
        {{ $this->form }}
    </x-filament-panels::form>

    <x-filament::button wire:click="save">
        Simpan
    </x-filament::button>
</x-filament-panels::page>
