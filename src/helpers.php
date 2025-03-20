<?php

use Backstage\Translations\Filament\TranslationsPlugin;

function country_flag($code)
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
