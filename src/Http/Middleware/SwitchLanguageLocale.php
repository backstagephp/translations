<?php

namespace Backstage\Translations\Filament\Http\Middleware;

use Backstage\Translations\Filament\Resources\LanguageResource;
use Closure;
use Illuminate\Http\Request;

class SwitchLanguageLocale
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (filamentTranslations()->isLanguageSwitcherDisabled()) {
            $preferredLocale = LanguageResource::getModel()::where('default', true)?->first();

            if (! $preferredLocale) {
                config(['languages.code' => 'en_US']);
                config(['languages.language_code' => 'en']);

                app()->setLocale(locale: 'en');

                return $next($request);
            }

            config(['languages.code' => $preferredLocale->code]);
            config(['languages.language_code' => $preferredLocale->languageCode]);

            app()->setLocale(
                locale: $preferredLocale->languageCode
            );

            return $next($request);
        }

        $preferredLocale = session()->get('languages')['language_code'] ??
            request()->get('locale') ??
            request()->cookie('filament_language_switch_locale') ??
            config('app.locale', 'en') ??
            request()->getPreferredLanguage();

        app()->setLocale(
            locale: $preferredLocale
        );

        return $next($request);
    }
}
