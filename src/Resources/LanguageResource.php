<?php

namespace Backstage\Translations\Filament\Resources;

use Backstage\Translations\Filament\Resources\LanguageResource\Pages\CreateLanguage;
use Backstage\Translations\Filament\Resources\LanguageResource\Pages\EditLanguage;
use Backstage\Translations\Filament\Resources\LanguageResource\Pages\ListLanguages;
use Backstage\Translations\Filament\TranslationsPlugin;
use Backstage\Translations\Laravel\Jobs\TranslateKeys;
use Backstage\Translations\Laravel\Models\Language;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\IconSize;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class LanguageResource extends Resource
{
    protected static ?string $model = Language::class;

    protected static ?string $slug = 'languages/translations';

    protected static bool $isScopedToTenant = false;

    public static function getCluster(): ?string
    {
        return config('backstage.translations.resources-cluster.language');
    }

    public static function canAccess(): bool
    {
        return TranslationsPlugin::get()->userCanManageTranslations();
    }

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

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label(__('Code'))
                    ->prefixIconColor('gray')
                    ->prefixIcon(fn ($state): ?string => $state ? country_flag($state) : 'heroicon-s-globe-alt')
                    ->unique(fn () => (new (static::getModel()))->getTable(), fn ($component) => $component->getName(), null, true)
                    ->live(debounce: 250)
                    ->columnSpan(2)
                    ->afterStateUpdated(function ($state, Set $set) {
                        $code = str_replace('_', '-', $state);

                        $set('name', localized_language_name($code));
                        $set('native', localized_language_name($code, explode('-', $code)[0]));
                    })
                    ->required(),

                TextInput::make('name')
                    ->label(__('Name'))
                    ->columnSpan(5)
                    ->required(),

                TextInput::make('native')
                    ->label(__('Native'))
                    ->columnSpan(5)
                    ->required(),

                Toggle::make('active')
                    ->label(__('Active'))
                    ->columnSpan(2)
                    ->default(true)
                    ->required(),

                Toggle::make('default')
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
                IconColumn::make('flag')
                    ->label('')
                    ->width(1)
                    ->getStateUsing(fn () => true)
                    ->icon(fn ($record): string => country_flag($record->languageCode))
                    ->color('danger')
                    ->size(fn () => IconSize::TwoExtraLarge)
                    ->url(fn (Language $record) => TranslationResource::getUrl('index', [
                        'tenant' => Filament::getTenant(),
                        'tableFilters[language][code]' => [$record->code],
                    ])),

                IconColumn::make('active')
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
                    ->url(fn (Language $record) => TranslationResource::getUrl('index', [
                        'tenant' => Filament::getTenant(),
                        'tableFilters[language][code]' => [$record->code],
                    ])),

                TextColumn::make('country')
                    ->label(__('Country'))
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => explode('-', $record->code)[1] ?? '')
                    ->url(fn (Language $record) => TranslationResource::getUrl('index', [
                        'tenant' => Filament::getTenant(),
                        'tableFilters[language][code]' => [$record->code],
                    ]))
                    ->visible(fn () => Language::active()->where('code', 'LIKE', '%-%')->distinct(DB::raw('SUBSTRING_INDEX(code, "-", -1)'))->count() > 1),

                IconColumn::make('default')
                    ->tooltip(__('This "default" setting is ignored if the Language Switcher is being used.'))
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

                TextColumn::make('native')
                    ->searchable()
                    ->visible(false),
            ])
            ->recordActions([
                Action::make('translate')
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
            'index' => ListLanguages::route('/'),
            'create' => CreateLanguage::route('/create'),
            'edit' => EditLanguage::route('/{record}/edit'),
        ];
    }
}
