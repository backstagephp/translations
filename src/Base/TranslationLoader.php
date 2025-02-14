<?php

namespace Backstage\Translations\Filament\Base;

use Backstage\Translations\Filament\Resources\LanguageResource;
use Backstage\Translations\Filament\Resources\TranslationResource;
use Backstage\Translations\Laravel\Models\Translation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Translation\FileLoader;

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

        $dbTranslations = TranslationResource::getModel()::select('key', 'text')
            ->where('code', 'LIKE', $locale . '_%')
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
