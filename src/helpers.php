<?php

use Backstage\Translations\TranslationsPlugin;

function getCountryFlag($locale)
{
    if ($locale == 'en') {
        return 'flag-country-us';
    }

    return 'flag-country-' . $locale;
}

function filamentTranslations(): TranslationsPlugin
{
    return TranslationsPlugin::get();
}
