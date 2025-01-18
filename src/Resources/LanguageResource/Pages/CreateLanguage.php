<?php

namespace Vormkracht10\FilamentTranslations\Resources\LanguageResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;
use Vormkracht10\FilamentTranslations\Resources\LanguageResource;
use Vormkracht10\LaravelTranslations\Jobs\ScanTranslatableKeys;
use Vormkracht10\LaravelTranslations\Jobs\TranslateKeys;

class CreateLanguage extends CreateRecord
{
    protected static string $resource = LanguageResource::class;

    public function getTitle(): string | Htmlable
    {
        return __('Create Language');
    }

    protected function beforeCreate(): void {}

    protected function afterCreate()
    {
        dispatch_sync(new ScanTranslatableKeys($this->record));
        dispatch(new TranslateKeys($this->record));
    }
}
