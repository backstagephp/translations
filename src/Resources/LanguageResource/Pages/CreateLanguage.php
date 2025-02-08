<?php

namespace Backstage\Translations\Resources\LanguageResource\Pages;

use Backstage\Translations\Laravel\Jobs\ScanTranslationStrings;
use Backstage\Translations\Resources\LanguageResource;
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
        dispatch(new ScanTranslationStrings($this->record));

        $this->redirect(EditLanguage::getUrl(['record' => $this->record->code]));
    }

}
