<?php

namespace Backstage\Translations\Http\Middleware;

use Backstage\Translations\Laravel\Models\Language;
use Closure;
use Illuminate\Http\Request;

class SwitchLanguageLocale
{
    public function handle(Request $request, Closure $next): mixed
    {
        $perferredLocale = request()->get('code') ?:
            session()->get('code') ?:
            request()->cookie('filament_language_code') ?:
            Language::where('default', true)->first()?->languageCode ?:
            request()->getPreferredLanguage() ?:
            app()->getLocale() ?: 'en';

        app()->setLocale($perferredLocale);

        return $next($request);
    }
}
