<?php

function getCountryFlag($locale)
{
    if ($locale == 'en') {
        return 'flag-country-us';
    }

    return 'flag-country-' . $locale;
}
