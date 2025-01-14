<?php

namespace Vormkracht10\FilamentTranslations;

use Illuminate\Support\Facades\DB;
use Illuminate\Translation\FileLoader;

class TranslationLoader extends FileLoader
{
    public function load($locale, $group, $namespace = null)
    {
        $namespace = $namespace ?: '*';

        if (! isset($this->translations[$locale][$group][$namespace])) {
            $translations = DB::table('translations')
                ->where('locale', $locale)
                ->where('group', $group)
                ->where('namespace', $namespace)
                ->pluck('text', 'key')
                ->toArray();

            $this->translations[$locale][$group][$namespace] = $translations;
        }

        return $this->translations[$locale][$group][$namespace];
    }
}
