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
            $perferredLocale = LanguageResource::getModel()::where('default', true)?->first();
            if (! $perferredLocale) {
                config(['languages.code' => 'en_US']);
                config(['languages.language_code' => 'en']);

                app()->setLocale(locale: 'en');
                
                return $next($request);
            }

            config(['languages.code' => $perferredLocale->code]);
            config(['languages.language_code' => $perferredLocale->languageCode]);

            app()->setLocale(
                locale: $perferredLocale->languageCode
            );

            return $next($request);
        }

        
        $perferredLocale = session()->get('languages')['language_code'] ??
            request()->get('locale') ??
            request()->cookie('filament_language_switch_locale') ??
            config('app.locale', 'en') ??
            request()->getPreferredLanguage();

        app()->setLocale(
            locale: $perferredLocale
        );

        return $next($request);
    }
}
