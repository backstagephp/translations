<?php

namespace Vormkracht10\FilamentTranslations\Resources\LanguageResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Vormkracht10\FilamentTranslations\Resources\LanguageResource;
use Vormkracht10\FilamentTranslations\Resources\TranslationResource;

class EditLanguage extends EditRecord
{
    protected static string $resource = LanguageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function beforeSave(): void
    {
        TranslationResource::getModel()::where('locale', $this->record['locale'])->get()->each(function ($translation) {
            $translation->update([
                'locale' => $this->data['locale'],
            ]);
        });
    }
}
