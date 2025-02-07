@if(!filamentTranslations()->isUsingAppLang())
<x-filament::dropdown wire:poll.1s placement="bottom-start">
    <x-slot name="trigger">
        <x-filament::icon :icon="$currentLanguageIcon" class="w-10 h-auto" />
    </x-slot>

    <x-filament::dropdown.list >
        @foreach ($languages as $language  => $label)
            <x-filament::dropdown.list.item wire:click="switchLanguage('{{ $language }}')">
            <div class="filament-dropdown-list-item-label truncate text-start flex justify-content-start gap-3">   
                <div>
                    <x-filament::icon :icon="getCountryFlag($language)" class="w-5 " />
                </div>
                <div>
                    {{ $label }}
                </div>
              </div>
            </x-filament::dropdown.list.item>
        @endforeach

        <x-filament::dropdown.list.item wire:click="list" class="mt-3">
            <div class="filament-dropdown-list-item-label truncate text-start flex justify-content-start gap-3">   
                <div>
                    <x-filament::icon icon="heroicon-m-map" class="w-5 " />
                </div>
                <div>
                   {{ __('Configure languages')}}
                </div>
              </div>
            </x-filament::dropdown.list.item>
    </x-filament::dropdown.list>
</x-filament::dropdown>
@else 
<span></span>
@endif