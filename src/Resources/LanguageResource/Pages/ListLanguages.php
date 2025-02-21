<?php

namespace Backstage\Translations\Filament\Resources\LanguageResource\Pages;

use Filament\Actions;
use Filament\Support\Colors\Color;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Backstage\Translations\Filament\Resources\LanguageResource;
use Backstage\Translations\Laravel\Jobs\ScanTranslationStrings;

class ListLanguages extends ListRecords
{
    protected static string $resource = LanguageResource::class;

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

                    return dispatch(new ScanTranslationStrings);
                })
                ->icon('heroicon-o-arrow-path'),
                
            Actions\CreateAction::make(),
        ];
    }
}
