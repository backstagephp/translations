<?php

namespace Backstage\Translations\Resources\TranslationResource\Pages;

use Backstage\Translations\Resources\LanguageResource;
use Backstage\Translations\Resources\TranslationResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Colors\Color;
use Vormkracht10\LaravelTranslations\Jobs\ScanTranslatableKeys;
use Vormkracht10\LaravelTranslations\Jobs\TranslateKeys;

class ListTranslations extends ListRecords
{
    protected static string $resource = TranslationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('translate')
                ->icon($this->getResource()::getNavigationIcon())
                ->label(__('Translate using :type', ['type' => ucfirst(config('translations.translation.driver'))]))
                ->color(fn () => Color::Green)
                ->action(function () {
                    $record = LanguageResource::getModel()::where('locale', config('app.locale'))->first();

                    dispatch(new TranslateKeys($record));

                    Notification::make()
                        ->title(__('Translations are being translated'))
                        ->body(__('Please wait a moment while the translations are being translated.'))
                        ->success()
                        ->send();
                })
                ->visible(fn () => filamentTranslations()->isUsingAppLang()),

            Actions\Action::make('rescan')
                ->label(__('Rescan'))
                ->color(Color::Rose)
                ->action(fn () => dispatch_sync(new ScanTranslatableKeys(redo: true)))
                ->icon('heroicon-o-arrow-path'),
        ];
    }
}
