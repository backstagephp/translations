<?php

namespace Vormkracht10\FilamentTranslations\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \FilamentTranslations\FilamentTranslations\FilamentTranslations
 */
class FilamentTranslations extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Vormkracht10\FilamentTranslations\FilamentTranslations::class;
    }
}
