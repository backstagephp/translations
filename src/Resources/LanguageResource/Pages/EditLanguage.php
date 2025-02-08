<?php

namespace Backstage\Translations\Filament\Resources\LanguageResource\Pages;

use Backstage\Translations\Filament\Resources\LanguageResource;
use Backstage\Translations\Filament\Resources\TranslationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditLanguage extends EditRecord
{
    protected static string $resource = LanguageResource::class;

    public function getTitle(): string | Htmlable
    {
        return $this->record->native ? $this->record->name . ' (' . $this->record->native . ')' : $this->record->name;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->after(fn () => TranslationResource::getModel()::where('code', $this->record['code'])->delete()),
        ];
    }

    protected function beforeSave(): void
    {
        TranslationResource::getModel()::where('code', $this->record['code'])
            ->get()
            ->each(fn ($translation) => $translation->update(['code' => $this->data['code']]));
    }

    protected function afterSave(): void
    {
        redirect($this->getUrl(['record' => $this->record->languageCode]));
    }
}
