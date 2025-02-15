<?php

namespace Backstage\Translations\Filament\Resources;

use Backstage\Translations\Filament\Resources\LanguageResource\Pages;
use Backstage\Translations\Laravel\Jobs\TranslateKeys;
use Backstage\Translations\Laravel\Models\Language;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Locale;

class LanguageResource extends Resource
{
    protected static ?string $model = Language::class;

    protected static ?string $slug = 'languages/translations';

    protected static bool $isScopedToTenant = false;

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-language';
    }

    public static function getNavigationGroup(): ?string
    {
        return config('backstage-translations.navigation.group') ?? __('Translations');
    }

    public static function getNavigationLabel(): string
    {
        return __('Languages');
    }

    public static function getModelLabel(): string
    {
        return __('Language');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Languages');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label(__('Code'))
                    ->prefixIconColor('gray')
                    ->prefixIcon(fn ($state): ?string => $state ? getCountryFlag($state) : 'heroicon-s-globe-alt')
                    ->unique(fn () => (new (static::getModel()))->getTable(), fn ($component) => $component->getName(), null, true)
                    ->live(debounce: 250)
                    ->columnSpan(2)
                    ->afterStateUpdated(function ($state, Set $set) {
                        $set('name', ucfirst(Locale::getDisplayLanguage(explode('_', $state)[0], app()->getLocale())));
                        $set('native', ucfirst(Locale::getDisplayLanguage(explode('_', $state)[0], explode('_', $state)[0])));
                    })
                    ->required(),

                Forms\Components\TextInput::make('name')
                    ->label(__('Name'))
                    ->columnSpan(5)
                    ->required(),

                Forms\Components\TextInput::make('native')
                    ->label(__('Native'))
                    ->columnSpan(5)
                    ->required(),

                Forms\Components\Toggle::make('active')
                    ->label(__('Active'))
                    ->columnSpan(2)
                    ->default(false)
                    ->required(),

                Forms\Components\Toggle::make('default')
                    ->label(__('Default'))
                    ->default(false)
                    ->inline()
                    ->required(),
            ])
            ->columns(12);
    }

    public static function table(Table $table): Table
    {
        $percentage = function ($record) {
            $translated = TranslationResource::getModel()::where('code', $record->code)
                ->whereNotNull('translated_at')
                ->count();

            $total = TranslationResource::getModel()::where('code', $record->code)
                ->count();

            if ($translated == 0 || $total == 0) {
                return 0;
            }

            $result = round($translated / $total * 100);

            return $result;
        };

        return $table
            ->columns([
                Tables\Columns\IconColumn::make('flag')
                    ->label('')
                    ->width(1)
                    ->getStateUsing(fn () => true)
                    ->icon(fn ($record): string => getCountryFlag($record->languageCode))
                    ->color('danger')
                    ->size(fn () => Tables\Columns\IconColumn\IconColumnSize::TwoExtraLarge),

                Tables\Columns\IconColumn::make('active')
                    ->label(__('Active'))
                    ->boolean()
                    ->action(fn ($record) => $record->update(['active' => ! $record->active])),

                Tables\Columns\IconColumn::make('default')
                    ->label(__('Default'))
                    ->boolean()
                    ->action(function ($record) {
                        if(!$record->active && ! $record->default) {
                            Notification::make()
                                ->title(__('Language not active'))
                                ->body(__('You can only set a language as default if it is active'))
                                ->danger()
                                ->send();
                                
                            return;
                        }

                        $record->update(['default' => ! $record->default]);

                        return redirect(request()->header('Referer'));
                    }),

                Tables\Columns\TextColumn::make('name')
                    ->label(__('Name'))
                    ->searchable()
                    ->description(fn ($record) => $record->native),

                Tables\Columns\TextColumn::make('code')
                    ->label(__('Code'))
                    ->badge()
                    ->color('gray'),

                \RyanChandler\FilamentProgressColumn\ProgressColumn::make('translated')
                    ->label('Translated')
                    ->poll('1s')
                    ->progress(fn ($record) => $percentage($record))
                    ->color(fn ($record) => $percentage($record) == 100 ? 'success' : 'danger'),
              
                    Tables\Columns\TextColumn::make('native')
                    ->searchable()
                    ->visible(false)
            ])
            ->actions([
                Tables\Actions\Action::make('translate')
                    ->icon('heroicon-o-arrow-path')
                    ->label(__('Translate'))
                    ->action(function ($record) {
                        dispatch(new TranslateKeys($record));

                        Notification::make()
                            ->title(__('Translations queued'))
                            ->body(__('The translations have been queued for translation'))
                            ->success()
                            ->send();
                    })
                    ->button(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLanguages::route('/'),
            'create' => Pages\CreateLanguage::route('/create'),
            'edit' => Pages\EditLanguage::route('/{record}/edit'),
        ];
    }
}
