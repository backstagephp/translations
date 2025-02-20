<?php

namespace Backstage\Translations\Filament\Resources\LanguageResource\Pages;

use Backstage\Translations\Filament\Resources\LanguageResource;
use Backstage\Translations\Laravel\Jobs\ScanTranslationStrings;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateLanguage extends CreateRecord
{
    protected static string $resource = LanguageResource::class;

    public function getTitle(): string | Htmlable
    {
        return __('Create Language');
    }

    protected function afterCreate()
    {
        $this->redirect(EditLanguage::getUrl(['record' => $this->record->code]));
    }
}
