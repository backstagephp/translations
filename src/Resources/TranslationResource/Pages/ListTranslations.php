<?php

namespace Backstage\Translations\Filament\Resources\TranslationResource\Pages;

use Filament\Actions\Action;
use Backstage\Translations\Filament\Resources\TranslationResource;
use Backstage\Translations\Laravel\Jobs\ScanTranslationStrings;
use Backstage\Translations\Laravel\Jobs\TranslateKeys;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Colors\Color;
use Illuminate\Support\Str;

class ListTranslations extends ListRecords
{
    protected static string $resource = TranslationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('scan')
                ->label(__('Scan'))
                ->color(Color::Blue)
                ->action(function () {
                    Notification::make()
                        ->title(__('Translations are being scanned'))
                        ->body(__('Please wait a moment while the translations are being scanned.'))
                        ->success()
                        ->send();

                    ScanTranslationStrings::dispatch();
                })
                ->icon('heroicon-o-arrow-path'),

            Action::make('translate')
                ->icon($this->getResource()::getNavigationIcon())
                ->label(__('Translate using :type', ['type' => Str::headline(config('translations.translators.default'))]))
                ->color(fn () => Color::Green)
                ->action(function () {
                    $record = config('backstage.translations.resources.language')::getModel()::where('code', config('app.locale'))->first();

                    TranslateKeys::dispatch($record);

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
