<x-filament::dropdown wire:poll.1s placement="bottom-start">
    <x-slot name="trigger">
        <x-filament::icon :icon="$currentLanguageIcon" class="w-6 h-auto" />
    </x-slot>

    <x-filament::dropdown.list >
        @foreach ($languages as $language  => $label)
            <x-filament::dropdown.list.item wire:click="switchLanguage('{{ $language }}')">
            <div class="flex gap-3 truncate filament-dropdown-list-item-label text-start justify-content-start">   
                <div>
                    <x-filament::icon :icon="{{ explode('-', $language)[0] }}" class="w-5" style="margin-top:3px;" />
                </div>
                <div>
                    {{ $label }}
                </div>
              </div>
            </x-filament::dropdown.list.item>
        @endforeach

        <x-filament::dropdown.list.item wire:click="list" class="mt-3">
            <div class="flex gap-3 truncate filament-dropdown-list-item-label text-start justify-content-start">   
                <div>
                    <x-filament::icon icon="heroicon-o-language" class="w-5" />
                </div>
                <div>
                   {{ __('Configure languages')}}
                </div>
              </div>
            </x-filament::dropdown.list.item>
    </x-filament::dropdown.list>
</x-filament::dropdown>