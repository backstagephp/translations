<?php

namespace Backstage\Translations\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SwitchLanguageLocale
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (filamentTranslations()->isUsingAppLang()) {
            app()->setLocale(
                locale: config('app.locale')
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
