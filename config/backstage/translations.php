<?php

return [
    'resources' => [
        'language' => Backstage\Translations\Filament\Resources\LanguageResource::class,
        'translation' => Backstage\Translations\Filament\Resources\TranslationResource::class,
    ],

    'navigation' => [
        // If null, default of __('Translations') will be used
        'group' => null,
    ],
];
