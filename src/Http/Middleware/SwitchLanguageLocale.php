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
                return $next($request);
            }

            app()->setLocale(
                locale: $preferredLocale->languageCode
            );

            return $next($request);
        }

        $preferredLocale = session('locale') ?:
            request()->get('locale') ?:
            LanguageResource::getModel()::where('code', str_replace('_', '-', request()->getPreferredLanguage()))->first()?->languageCode ?:
            config('app.locale', 'en');

        app()->setLocale(
            locale: $preferredLocale
        );

        return $next($request);
    }
}
