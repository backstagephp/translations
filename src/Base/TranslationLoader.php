<?php

namespace Backstage\Translations\Filament\Base;

use Illuminate\Support\Facades\Schema;
use Illuminate\Translation\FileLoader;
use Backstage\Translations\Filament\TranslationsPlugin;
use Backstage\Translations\Filament\FilamentTranslations;
use Backstage\Translations\Filament\Resources\LanguageResource;
use Backstage\Translations\Filament\Resources\TranslationResource;
use Backstage\Translations\Laravel\Models\Translation;

class TranslationLoader extends FileLoader
{
    public function load($locale, $group, $namespace = null)
    {
        $fileTranslations = parent::load($locale, $group, $namespace);

        if (
            ! Schema::hasTable((new Translation)->getTable()) ||
            ! is_null($namespace) && $namespace !== '*'
        ) {
            return $fileTranslations;
        }

        $locale = LanguageResource::getModel()::where('code', session()->get('languages')['code'])
            ->first()?->code ?: LanguageResource::getModel()::first()?->code ?: '';

        $dbTranslations = TranslationResource::getModel()::select('key', 'text')
            ->where('code', $locale)
            ->pluck('text', 'key')
            ->toArray();

            if ($dbTranslations) {
            return $dbTranslations + $fileTranslations;
        }
        return $fileTranslations;
    }

    public function addNamespace($namespace, $hint) {}

    public function addJsonPath($path) {}

    public function namespaces() {}
}
