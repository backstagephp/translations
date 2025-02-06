<?php

namespace Vormkracht10\FilamentTranslations\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Vormkracht10\FilamentTranslations\FilamentTranslationsPlugin;

class SwitchLanguageLocale
{
    public function handle(Request $request, Closure $next): mixed
    {
        $absoluteLang = FilamentTranslationsPlugin::get()->absoluteLang;

        if($absoluteLang) {
            app()->setLocale(
                locale: $absoluteLang
            );

            return $next($request);
        }
        
        $perferredLocale = session()->get('locale') ??
            request()->get('locale') ??
            request()->cookie('filament_language_switch_locale') ??
            config('app.locale', 'en') ??
            request()->getPreferredLanguage();

        app()->setLocale(
            locale: $perferredLocale ?: config('app.locale')
        );

        return $next($request);
    }
}
