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
            $preferredLocale = LanguageResource::getModel()::default();

            if (! $preferredLocale) {
                return $next($request);
            }

            app()->setLocale(
                locale: $preferredLocale->languageCode
            );

            session(['language' => $preferredLocale->only('code', 'name', 'native', 'localizedLanguageName', 'localizedCountryName')]);

            view()->share('preferredLocale', $preferredLocale);

            return $next($request);
        }

        $preferredLocale = LanguageResource::getModel()::where('code', session('language')['code'] ?? '')->first() ?:
            LanguageResource::getModel()::where('code', str_replace('_', '-', (string) request()->getPreferredLanguage()))->first() ?:
            LanguageResource::getModel()::default();

        if ($preferredLocale) {
            session(['language' => $preferredLocale->only('code', 'name', 'native', 'localizedLanguageName', 'localizedCountryName')]);

            app()->setLocale(
                locale: $preferredLocale->languageCode
            );

            view()->share('preferredLocale', $preferredLocale);
        }

        return $next($request);
    }
}
