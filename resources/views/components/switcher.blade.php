<div>
    @if(count($languages))
        <x-filament::dropdown placement="bottom-start" teleport>
            <x-slot name="trigger">
                <x-filament::icon :icon="country_flag($currentLanguage->languageCode)" class="w-6 h-auto" />
            </x-slot>

            <x-filament::dropdown.list>
                @foreach ($languages as $language)
                    <x-filament::dropdown.list.item wire:click="switchLanguage('{{ $language->code }}')">
                    <div class="flex gap-3 truncate filament-dropdown-list-item-label text-start justify-content-start">
                        <div>
                            <x-filament::icon :icon="country_flag($language->languageCode)" class="w-5" style="margin-top:3px;" />
                        </div>
                        <div>
                            {{ $language->localizedLanguageName }}
                        </div>
                    </div>
                    </x-filament::dropdown.list.item>
                @endforeach

                @if(filamentTranslations()->userCanManageTranslations())
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
                @endif
            </x-filament::dropdown.list>
        </x-filament::dropdown>
    @endif
</div>
