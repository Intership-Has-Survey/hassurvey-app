{{-- resources/views/filament/resources/pemilik-resource/pages/edit-pemilik.blade.php --}}

<x-filament-panels::page>

    {{-- 1. Render Form Utama seperti biasa --}}
    {{ $this->form }}

    {{-- 2. Panggil dan Render WIDGET Anda di sini --}}
    @if (count($this->getContentWidgets()))
        <x-filament-widgets::widgets :widgets="$this->getContentWidgets()" :columns="$this->getContentWidgetsColumns()"
            :data="['record' => $this->record]" class="mt-6" />
    @endif

    {{-- 3. Render Relation Manager setelah widget --}}
    @if (count($this->getRelationManagers()))
        <x-filament-panels::resources.relation-managers :active-manager="$this->activeRelationManager"
            :managers="$this->getRelationManagers()" :owner-record="$this->record" :page-class="static::class" />
    @endif

</x-filament-panels::page>