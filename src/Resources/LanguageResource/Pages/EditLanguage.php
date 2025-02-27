<?php

namespace Backstage\Translations\Filament\Resources\LanguageResource\Pages;

use Backstage\Translations\Filament\Resources\LanguageResource;
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
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        redirect($this->getUrl(['record' => $this->record->languageCode]));
    }
}
