<?php

namespace Backstage\Translations\Filament\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class SwitchLanguageLocale
{
    public function handle(Request $request, Closure $next): mixed
    {
        $model = config('backstage.translations.resources.language')::getModel();

        if (filamentTranslations()->isLanguageSwitcherDisabled()) {
            $preferredLanguage = $model::default();

            if (! $preferredLanguage) {
                return $next($request);
            }

            $this->saveLanguageForUser($preferredLanguage);
            $this->setLanguageInSession($preferredLanguage);

            app()->setLocale(
                locale: $preferredLanguage->languageCode
            );

            view()->share('preferredLanguage', $preferredLanguage);

            return $next($request);
        }

        $preferredLanguage = $model::where('code', session('language')['code'] ?? '')->first() ?:
            $model::find(auth()->user()->locale) ?:
            $model::where('code', str_replace('_', '-', (string) request()->getPreferredLanguage()))->first() ?:
            $model::default();

        if ($preferredLanguage) {
            $this->saveLanguageForUser($preferredLanguage);
            $this->setLanguageInSession($preferredLanguage);

            app()->setLocale(
                locale: $preferredLanguage->languageCode
            );

            view()->share('preferredLanguage', $preferredLanguage);
        }

        return $next($request);
    }

    public function setLanguageInSession(Model $language): void
    {
        session(['language' => $language->only('code', 'name', 'native', 'localizedLanguageName', 'localizedCountryName')]);
    }

    public function saveLanguageForUser(Model $language): void
    {
        if(auth()->user()->hasAttribute('locale')) {
            auth()->user()->update(['locale' => $language->code]);
        }
    }
}
