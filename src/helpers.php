<?php

use Backstage\Translations\TranslationsPlugin;
use Backstage\Translations\Resources\LanguageResource;

function getCountryFlag($locale)
{
    $locale = explode('_', $locale)[0];

    if ($locale == 'en') {
        return 'flag-country-us';
    }

    return 'flag-country-' . $locale;
}

function filamentTranslations(): TranslationsPlugin
{
    return TranslationsPlugin::get();
}
