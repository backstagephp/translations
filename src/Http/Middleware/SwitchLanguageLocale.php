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

            session(['locale' => $preferredLocale]);

            view()->share('preferredLocale', $preferredLocale);

            return $next($request);
        }

        $preferredLocale = session('locale') ?:
            LanguageResource::getModel()::where('code', str_replace('_', '-', (string) request()->getPreferredLanguage()))->first() ?:
            LanguageResource::getModel()::default();

        if ($preferredLocale) {
            session(['locale' => $preferredLocale]);

            app()->setLocale(
                locale: $preferredLocale->languageCode
            );

            view()->share('preferredLocale', $preferredLocale);
        }

        return $next($request);
    }
}
