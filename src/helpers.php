<?php

use Vormkracht10\FilamentTranslations\FilamentTranslationsPlugin;

function getCountryFlag($locale)
{
    if ($locale == 'en') {
        return 'flag-country-us';
    }

    return 'flag-country-' . $locale;
}

function filamentTranslations(): FilamentTranslationsPlugin
{
    return FilamentTranslationsPlugin::get();
}
