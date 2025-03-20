<?php

use Backstage\Translations\Filament\TranslationsPlugin;

function getCountryFlag($code)
{
    $code = explode('-', $code)[0];

    if ($code == 'en') {
        return 'flag-country-us';
    }

    return 'flag-country-' . $code;
}

function filamentTranslations(): TranslationsPlugin
{
    return TranslationsPlugin::get();
}
