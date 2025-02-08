<?php

namespace Backstage\Translations\Resources\TranslationResource\Pages;

use Backstage\Translations\Laravel\Jobs\ScanTranslationStrings;
use Backstage\Translations\Laravel\Jobs\TranslateKeys;
use Backstage\Translations\Resources\LanguageResource;
use Backstage\Translations\Resources\TranslationResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Colors\Color;

class ListTranslations extends ListRecords
{
    protected static string $resource = TranslationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('scan')
                ->label(__('Scan'))
                ->color(Color::Blue)
                ->action(function () {
                    Notification::make()
                        ->title(__('Translations are being scanned'))
                        ->body(__('Please wait a moment while the translations are being scanned.'))
                        ->success()
                        ->send();

                    return dispatch(new ScanTranslationStrings(redo: true));
                })
                ->icon('heroicon-o-arrow-path'),

            Actions\Action::make('translate')
                ->icon($this->getResource()::getNavigationIcon())
                ->label(__('Translate using :type', ['type' => ucfirst(config('translations.translators.default'))]))
                ->color(fn () => Color::Green)
                ->action(function () {
                    $record = LanguageResource::getModel()::where('code', config('app.locale'))->first();

                    dispatch(new TranslateKeys($record));

                    Notification::make()
                        ->title(__('Translations are being translated'))
                        ->body(__('Please wait a moment while the translations are being translated.'))
                        ->success()
                        ->send();
                })
                ->visible(fn () => config('translations.translators.default'))
                ->disabled(fn () => $this->getResource()::getModel()::count() === 0),
        ];
    }
}
