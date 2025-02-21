<?php

namespace Backstage\Translations\Filament\Resources\LanguageResource\Pages;

use Backstage\Translations\Filament\Resources\LanguageResource;
use Backstage\Translations\Laravel\Jobs\TranslateKeys;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;

class CreateLanguage extends CreateRecord
{
    protected static string $resource = LanguageResource::class;

    protected $langRequiresTranslation = false;

    public function getTitle(): string | Htmlable
    {
        return __('Create Language');
    }

    public function beforeCreate()
    {
        $this->langRequiresTranslation = $this->data['translate_after_creation'];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['translate_after_creation']);

        return $data;
    }

    protected function afterCreate()
    {
        if ($this->langRequiresTranslation) {
            dispatch(new TranslateKeys($this->record))
                ->delay(now()->addSeconds(30));
        }

        $this->redirect(EditLanguage::getUrl(['record' => $this->record->code]));
    }
}
