<x-filament-panels::page>
    <x-filament-panels::form>
        {{ $this->form }}
        {{-- <x-filament-panels::form.actions :actions="$this->getFormAction()" /> --}}
    </x-filament-panels::form>

    <x-filament::button wire:click="save">
        Simpan
    </x-filament::button>

    <x-filament::button tag="a" href="{{ $this->getPreviewUrl() }}" target="_blank" icon="heroicon-o-eye"
        color="success">
        Preview PDF Template
    </x-filament::button>
</x-filament-panels::page>
