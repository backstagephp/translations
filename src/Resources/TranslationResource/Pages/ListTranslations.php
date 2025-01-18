<?php

namespace Vormkracht10\FilamentTranslations\Resources\TranslationResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Colors\Color;
use Vormkracht10\FilamentTranslations\Resources\TranslationResource;
use Vormkracht10\LaravelTranslations\Jobs\ScanTranslatableKeys;

class ListTranslations extends ListRecords
{
    protected static string $resource = TranslationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('rescan')
                ->label(__('Rescan'))
                ->color(Color::Rose)
                ->action(fn () => dispatch(new ScanTranslatableKeys())),
        ];
    }
    
}
