<?php

use Filament\Actions\Exports\Enums\ExportFormat;

return [
    'resources' => [
        'language' => Backstage\Translations\Filament\Resources\LanguageResource::class,
        'translation' => Backstage\Translations\Filament\Resources\TranslationResource::class,
    ],

    'navigation' => [
        // If null, default of __('Translations') will be used
        'group' => null,
    ],

    'importer' => [
        'class' => Backstage\Translations\Filament\Imports\TranslationImporter::class,
    ],

    'exporter' => [
        'class' => Backstage\Translations\Filament\Exports\TranslationExporter::class,
        'formats' => [
            ExportFormat::Csv,
            ExportFormat::Xlsx,
        ],
    ],
];
