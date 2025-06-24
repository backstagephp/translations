<?php

namespace Backstage\Translations\Filament;

use Backstage\Translations\Laravel\Facades\Translator;
use Backstage\Translations\Laravel\Models\Language;
use Filament\Actions\Action;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Set;

class Translations
{
    public static function registerMacro(): void
    {
        Field::macro('canTranslate', function ($hint = true) {
            $this->live();

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
                        ->schema([
                            Select::make('language')
                                ->label(__('Language'))
                                ->helperText(__('Select the language to translate to'))
                                ->options(Language::active()->get()->pluck('native', 'code')),
                        ])
                        ->hidden(function ($get, Field $component) {
                            return $get($component->getName()) == null;
                        })
                        ->action(function ($get, Field $component, $data) {
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
