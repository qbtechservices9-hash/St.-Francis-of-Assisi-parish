<x-filament-panels::page>
    <form wire:submit.prevent="save" class="space-y-6" enctype="multipart/form-data">
        {{ $this->form }}

        <x-filament::button type="submit">
            Save Home Page
        </x-filament::button>
    </form>
</x-filament-panels::page>


