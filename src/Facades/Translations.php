<?php

namespace Backstage\Translations\Filament\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \FilamentTranslations\FilamentTranslations\FilamentTranslations
 */
class BackstageTranslations extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Backstage\Translations\Filament\FilamentTranslations::class;
    }
}
