<?php

namespace Backstage\Translations\Resources\LanguageResource\Pages;

use Backstage\Translations\Resources\LanguageResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;
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
