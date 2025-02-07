<?php

namespace Backstage\Translations\Resources\LanguageResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use Backstage\Translations\Resources\LanguageResource;
use Backstage\Translations\Resources\TranslationResource;

class EditLanguage extends EditRecord
{
    protected static string $resource = LanguageResource::class;

    public function getTitle(): string | Htmlable
    {
        return $this->record->label;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->after(fn () => TranslationResource::getModel()::where('locale', $this->record['locale'])->delete()),
        ];
    }

    protected function beforeSave(): void
    {
        TranslationResource::getModel()::where('locale', $this->record['locale'])
            ->get()
            ->each(fn ($translation) => $translation->update(['locale' => $this->data['locale']]));
    }
}
