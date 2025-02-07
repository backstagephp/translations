<?php

namespace Backstage\Translations\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \FilamentTranslations\FilamentTranslations\FilamentTranslations
 */
class BackstageTranslations extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Backstage\Translations\FilamentTranslations::class;
    }
}
