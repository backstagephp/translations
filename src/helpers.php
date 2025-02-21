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

function getLocalizedCountryName(string $code, string $locale = null): string
{
    $code = strtolower(explode('-', $code)[1] ?: $code);

    return Locale::getDisplayRegion('-'.$code, $locale ??app()->getLocale());
}

function getLocalizedLanguageName(string $code, string $locale = null): string
{
    $code = strtolower(explode('-', $code)[0]);

    return Locale::getDisplayLanguage($code, $locale ??app()->getLocale());
}
