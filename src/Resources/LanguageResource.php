<?php

namespace Backstage\Translations\Filament\Resources;

use Backstage\Translations\Filament\Resources\LanguageResource\Pages;
use Backstage\Translations\Laravel\Jobs\TranslateKeys;
use Backstage\Translations\Laravel\Models\Language;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

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
        return config('backstage.translations.navigation.group') ?? __('Translations');
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
                    ->prefixIcon(fn ($state): ?string => $state ? country_flag($state) : 'heroicon-s-globe-alt')
                    ->unique(fn () => (new (static::getModel()))->getTable(), fn ($component) => $component->getName(), null, true)
                    ->live(debounce: 250)
                    ->columnSpan(2)
                    ->afterStateUpdated(function ($state, Set $set) {
                        $code = str_replace('_', '-', $state);

                        $set('name', getLocalizedLanguageName($code));
                        $set('native', getLocalizedLanguageName($code, explode('-', $code)[0]));
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
                    ->default(true)
                    ->required(),

                Forms\Components\Toggle::make('default')
                    ->label(__('Default'))
                    ->default(false)
                    ->inline()
                    ->required(),

                Forms\Components\Checkbox::make('translate_after_creation')
                    ->label(__('Translate after creation'))
                    ->default(true)
                    ->columnSpan(12)
                    ->helperText(__('This is translate all the keys after automatically scanning the project for translation keys.'))
                    ->visibleOn('create'),
            ])
            ->columns(12);
    }

    public static function table(Table $table): Table
    {
        $percentage = function ($record) {
            $translated = config('backstage.translations.resources.translation')::getModel()::where('code', $record->code)
                ->whereNotNull('translated_at')
                ->count();

            $total = config('backstage.translations.resources.translation')::getModel()::where('code', $record->code)
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
                    ->icon(fn ($record): string => country_flag($record->languageCode))
                    ->color('danger')
                    ->size(fn () => Tables\Columns\IconColumn\IconColumnSize::TwoExtraLarge)
                    ->url(fn (Language $record) => route('filament.' . Filament::getCurrentPanel()->getId() . '.resources.translations.index', [
                        'tenant' => Filament::getTenant(),
                        'tableFilters[language][code]' => [$record->code],
                    ])),

                Tables\Columns\IconColumn::make('active')
                    ->width(1)
                    ->label(__('Active'))
                    ->boolean()
                    ->alignCenter()
                    ->action(fn ($record) => $record->update(['active' => ! $record->active])),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->separator('')
                    ->description(fn ($record) => $record->code)
                    ->url(fn (Language $record) => route('filament.' . Filament::getCurrentPanel()->getId() . '.resources.translations.index', [
                        'tenant' => Filament::getTenant(),
                        'tableFilters[language][code]' => [$record->code],
                    ])),

                TextColumn::make('country')
                    ->label(__('Country'))
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => explode('-', $record->code)[1] ?? '')
                    ->url(fn (Language $record) => route('filament.' . Filament::getCurrentPanel()->getId() . '.resources.translations.index', [
                        'tenant' => Filament::getTenant(),
                        'tableFilters[language][code]' => [$record->code],
                    ]))
                    ->visible(fn () => Language::active()->where('code', 'LIKE', '%-%')->distinct(DB::raw('SUBSTRING_INDEX(code, "-", -1)'))->count() > 1),

                Tables\Columns\IconColumn::make('default')
                    ->label(__('Default'))
                    ->width(1)
                    ->alignCenter()
                    ->boolean()
                    ->action(function ($record) {
                        if (! $record->active && ! $record->default) {
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

                \RyanChandler\FilamentProgressColumn\ProgressColumn::make('translated')
                    ->label('Translated')
                    ->width('25%')
                    ->alignRight()
                    ->poll(fn ($record) => $percentage($record) < 100 ? '1s' : null)
                    ->progress(fn ($record) => $percentage($record))
                    ->color(fn ($record) => $percentage($record) == 100 ? 'success' : 'danger')
                    ->url(fn (Language $record) => route('filament.' . Filament::getCurrentPanel()->getId() . '.resources.translations.index', [
                        'tenant' => Filament::getTenant(),
                        'tableFilters[language][code]' => [$record->code],
                    ])),

                Tables\Columns\TextColumn::make('native')
                    ->searchable()
                    ->visible(false),
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

                EditAction::make()
                    ->modal()
                    ->modalHeading(__('Edit Language'))
                    ->modalDescription(fn ($record) => $record->key)
                    ->modalIcon(fn ($record) => country_flag($record->languageCode))
                    ->modalIconColor(null),
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
