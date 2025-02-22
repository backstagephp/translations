<?php

namespace Backstage\Translations\Filament;

use EchoLabs\Prism\Exceptions\PrismException;
use EchoLabs\Prism\Prism;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;

class Translations
{
    public static function registerMacro(): void
    {
        Field::macro('canTranslate', function ($prompt = null) {
            return $this->hintAction(
                function (Set $set, Field $component) use ($prompt) {
                    return Action::make('ai')
                        ->icon(config('ai.action.icon'))
                        ->label(config('ai.action.label'))
                        ->modalHeading(config('ai.action.modal.heading'))
                        ->modalSubmitActionLabel('Generate')
                        ->form([
                            Select::make('model')
                                ->label('Model')
                                ->options(
                                    collect(config('ai.providers'))
                                        ->mapWithKeys(fn ($provider, $model) => [
                                            $model => $model . ' (' . $provider->name . ')',
                                        ]),
                                )
                                ->default(key(config('ai.providers'))),

                            Textarea::make('prompt')
                                ->label('Prompt')
                                ->autosize()
                                ->default($prompt),

                            Section::make('configuration')
                                ->heading('Configuration')
                                ->schema([
                                    TextInput::make('temperature')
                                        ->numeric()
                                        ->label('Temperature')
                                        ->default(config('ai.configuration.temperature'))
                                        ->helperText('The higher the temperature, the more creative the text')
                                        ->maxValue(1)
                                        ->minValue(0)
                                        ->step('0.1'),
                                    TextInput::make('max_tokens')
                                        ->numeric()
                                        ->label('Max tokens')
                                        ->default(config('ai.configuration.max_tokens'))
                                        ->helperText('The maximum number of tokens to generate')
                                        ->step('10')
                                        ->minValue(0)
                                        ->suffixAction(
                                            Action::make('increase')
                                                ->icon('heroicon-o-plus')
                                                ->action(fn (Set $set, Get $get) => $set('max_tokens', $get('max_tokens') + 100)),
                                        ),
                                ])
                                ->columns(2)
                                ->collapsed()
                                ->collapsible(),
                        ])
                        ->action(function ($data) use ($component, $set) {
                            try {
                                $response = Prism::text()
                                    ->using(config('ai.providers.' . $data['model']), $data['model'])
                                    ->withPrompt($data['prompt'])
                                    ->generate();

                                $set($component->getName(), $response->text);
                            } catch (PrismException $exception) {
                                Notification::make()
                                    ->title('Text generation failed')
                                    ->body('Error: ' . $exception->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        });
                }
            );
        });
    }
}
