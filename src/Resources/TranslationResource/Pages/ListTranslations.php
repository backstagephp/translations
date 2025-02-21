<?php

namespace Backstage\Translations\Filament\Resources\TranslationResource\Pages;

use Filament\Actions;
use Illuminate\Support\Str;
use Filament\Support\Colors\Color;
use Illuminate\Support\HtmlString;
use Filament\Support\Enums\MaxWidth;
use Filament\Support\Enums\Alignment;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Backstage\Translations\Laravel\Jobs\TranslateKeys;
use Backstage\Translations\Filament\Resources\LanguageResource;
use Backstage\Translations\Laravel\Jobs\ScanTranslationStrings;
use Backstage\Translations\Filament\Resources\TranslationResource;
use Backstage\Translations\Filament\Resources\LanguageResource\Pages\CreateLanguage;

class ListTranslations extends ListRecords
{
    protected static string $resource = TranslationResource::class;

    public $defaultAction = 'checkLanguages';

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

            Actions\Action::make('translate')
                ->icon($this->getResource()::getNavigationIcon())
                ->label(__('Translate using :type', ['type' => Str::headline(config('translations.translators.default'))]))
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

    public function checkLanguages(): Actions\Action
    {
        return Actions\Action::make('checkLanguages')
            ->visible(fn () => LanguageResource::getModel()::count() === 0)
            ->label(__('Check languages'))
            ->color(Color::Blue)
            ->modalHeading((config('app.name')))
            ->modalContent(new HtmlString('<center>' . __('Please create a language first!') . '</center>'))
            ->modalIcon('heroicon-o-language')
            ->modalIconColor('warning')
            ->modalAlignment(Alignment::Center)
            ->modalFooterActionsAlignment(Alignment::Center)
            ->modalWidth(MaxWidth::Medium)
            ->modalCancelAction(false)
            ->closeModalByEscaping(false)
            ->modalCloseButton(false)
            ->closeModalByClickingAway(false)
            ->modalSubmitAction()
            ->modalSubmitActionLabel(__('Create language'))
            ->action(fn () => redirect(CreateLanguage::getUrl()));

    }
}
