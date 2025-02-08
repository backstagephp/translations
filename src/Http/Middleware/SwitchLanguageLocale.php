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
                session()->put('languages.code', 'en_US');
                session()->put('languages.language_code', 'en');

                return $next($request);
            }

            session()->put('languages.code', $perferredLocale->code);
            session()->put('languages.language_code', $perferredLocale->languageCode);

            return $next($request);
        }

        return $next($request);
    }
}
