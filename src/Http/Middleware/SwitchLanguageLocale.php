<?php

namespace Vormkracht10\FilamentTranslations\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Vormkracht10\FilamentTranslations\Components\Switcher;

class SwitchLanguageLocale
{
    public function handle(Request $request, Closure $next): mixed
    {
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