<?php

namespace Vormkracht10\FilamentTranslations\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;
use Vormkracht10\FilamentTranslations\Models\Translation;
use Vormkracht10\FilamentTranslations\Resources\LanguageResource;
use Vormkracht10\FilamentTranslations\Resources\TranslationResource;

class FilamentTranslationsCommand extends Command
{
    public $signature = 'filament-translations-dev:import';

    public $description = 'Import translations from Filament';

    public function handle()
    {
        $functions = [
            'trans',
            'trans_choice',
            'Lang::get',
            'Lang::choice',
            'Lang::trans',
            'Lang::transChoice',
            '@lang',
            '@choice',
            '__',
        ];

        $paths = [
            base_path('app'),
            base_path('resources/views'),
            base_path('vendor/filament'),
        ];

        foreach ($paths as $path) {
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));

            foreach ($files as $file) {
                if ($file->isFile() && in_array($file->getExtension(), ['php', 'blade.php'])) {
                    $content = file_get_contents($file->getRealPath());
                    foreach ($functions as $function) {
                        if (preg_match_all("/$function\(['\"](.+?)['\"]\)/", $content, $matches)) {
                            foreach ($matches[1] as $match) {
                                $translations[] = $match;
                            }
                        }
                    }
                }
            }
        }

        $locales = LanguageResource::getModel()::pluck('locale');

        $baseLocal = App::getLocale();

        $translations = collect($translations)->unique();

        $end = $translations->flatMap(function ($translation) use ($locales) {
            return $locales->map(function ($locale) use ($translation) {
                App::setLocale($locale);
                App::setFallbackLocale($locale);

                return [
                    'locale' => $locale,
                    'key' => $translation,
                    'text' => Lang::hasForLocale($translation, $locale) ? Lang::get($translation, [], $locale, $locale) : null,
                    'namespace' => $this->getNamespace($translation),
                ];
            });
        });

        App::setLocale($baseLocal);

        $end->each(function ($translation) {
            if (! is_array($translation['text'])) {
                TranslationResource::getModel()::updateOrCreate([
                    'group' => null, // WIP
                    'locale' => $translation['locale'],
                    'key' => $translation['key'],
                    'namespace' => $translation['namespace'],
                ], [
                    'text' => $translation['text'],
                ]);

                LanguageResource::getModel()::updateOrCreate([
                    'locale' => $translation['locale'],
                ], [
                    'locale' => $translation['locale'],
                ]);
                
            }
        });
    }

    protected function getNamespace($translation): string
    {
        if (str_contains($translation, '::')) {
            return explode(separator: '::', string: $translation)[0];
        }

        return '*';
    }
}
