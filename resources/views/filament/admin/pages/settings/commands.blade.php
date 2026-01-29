<x-filament-panels::page>
    <x-filament::section>
        <div class="flex gap-x-3">
            <x-filament::button wire:click="clearOptimize" color="danger">
                Optimize Clear
            </x-filament::button>

            <x-filament::button wire:click="clearView" color="info">
                Clear Views
            </x-filament::button>
        </div>
    </x-filament::section>
</x-filament-panels::page>