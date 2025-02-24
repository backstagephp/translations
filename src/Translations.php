<?php

namespace Backstage\Translations\Filament;

use Filament\Forms\Set;
use Filament\Support\Colors\Color;
use Filament\Forms\Components\Field;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Actions\Action;

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
                        ->action(function ($data) use ($component, $set) {
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
