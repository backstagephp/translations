<?php

namespace Backstage\Translations\Filament;

use Filament\Forms\Set;
use Filament\Forms\Components\Field;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Actions\Action;
use Backstage\Translations\Laravel\Facades\Translator;
use Backstage\Translations\Laravel\Managers\TranslatorManager;
use Backstage\Translations\Laravel\Models\Language;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\App;

class Translations
{
    public static function registerMacro(): void
    {
        Field::macro('canTranslate', function ($hint = true) {
            return $this->{$hint ? 'hintAction' : 'suffixAction'}(
                function (Set $set, Field $component) use ($hint) {
                    return Action::make('translate')
                        ->icon('heroicon-s-flag')
                        ->label('Translate')
                        ->color('gray')
                        ->extraAttributes($hint ? [
                            'x-show' => 'focused || hover',
                            'x-cloak' => '',
                        ] : [])
                        ->form([
                            Select::make('language')
                                ->label(__('Language'))
                                ->helperText(__('Select the language to translate to'))
                                ->options(Language::active()->get()->pluck('native', 'code'))
                        ])
                        ->action(function ($get, Field  $component, $data) {
                            $stringToTranslate = $get($component->getName());

                            $translator = Translator::with(config('translations.translators.default'));

                            $translated = $translator->translate($stringToTranslate, $data['language']);

                            $component->state($translated);

                            Notification::make()
                                ->title(__('Text is translated'))
                                ->body('Translated')
                                ->success()
                                ->send();
                        });
                }
            )->extraFieldWrapperAttributes([
                'x-data' => '{focused: false, hover: false}',
                'x-on:mouseover' => 'hover = true',
                'x-on:mouseout' => 'hover = false',
            ])->extraInputAttributes([
                'x-on:focus' => 'focused = true',
                'x-on:blur' => 'focused = false',
            ]);
        });
    }
}
