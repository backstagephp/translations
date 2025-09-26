<?php

namespace Backstage\Translations\Filament\Resources;

use Backstage\Translations\Filament\Resources\TranslationResource\Pages\CreateTranslation;
use Backstage\Translations\Filament\Resources\TranslationResource\Pages\ListTranslations;
use Backstage\Translations\Filament\TranslationsPlugin;
use Backstage\Translations\Laravel\Models\Language;
use Backstage\Translations\Laravel\Models\Translation;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ImportAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\IconSize;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;

class TranslationResource extends Resource
{
    protected static ?string $model = Translation::class;

    protected static ?string $slug = 'translations';

    protected static bool $isScopedToTenant = false;

    public static function getCluster(): ?string
    {
        return config('backstage.translations.resources-cluster.translation');
    }

    public static function canAccess(): bool
    {
        return TranslationsPlugin::get()->userCanManageTranslations();
    }

    public static function getNavigationParentItem(): ?string
    {
        return config('backstage.translations.resources.language')::getNavigationLabel();
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-language';
    }

    public static function getNavigationGroup(): ?string
    {
        return config('backstage.translations.resources.language')::getNavigationGroup();
    }

    public static function getNavigationLabel(): string
    {
        return __('Translations');
    }

    public static function getModelLabel(): string
    {
        return __('Translation');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Translations');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextArea::make('text')
                    ->label(__('Text'))
                    ->autosize()
                    ->autocomplete(false)
                    ->columnSpanFull()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ImportAction::make('import_translations')
                    ->label(__('Import translations'))
                    ->icon('heroicon-m-arrow-down-tray')
                    ->importer(config('backstage.translations.importer.class'))
                    ->color(fn () => Color::Gray),

                ExportAction::make('export_translations')
                    ->label(__('Export translations'))
                    ->exporter(config('backstage.translations.exporter.class'))
                    ->icon('heroicon-m-arrow-up-tray')
                    ->color(fn () => Color::Gray)
                    ->disabled(fn () => static::getModel()::count() === 0),
            ])
            ->columns([
                IconColumn::make('code')
                    ->label('')
                    ->sortable()
                    ->icon(fn ($record): string => country_flag($record->languageCode))
                    ->color('danger')
                    ->size(fn () => IconSize::TwoExtraLarge),

                TextColumn::make('key')
                    ->label(__('Key'))
                    ->searchable()
                    ->width('50%')
                    ->limit(50)
                    ->description(fn ($record) => $record->group)
                    ->sortable(),

                TextInputColumn::make('text')
                    ->label(__('Text'))
                    ->searchable()
                    ->width('50%')
                    ->sortable()
                    ->translated(),
            ])
            ->recordActions([
                EditAction::make()
                    ->modalHeading(__('Edit Translation'))
                    ->modalDescription(fn ($record) => $record->key)
                    ->modalIcon(fn ($record) => country_flag($record->languageCode))
                    ->modalIconColor(null)
                    ->mountUsing(function ($form, $record) {
                        $form->fill([
                            'text' => $record->text,
                        ]);
                    })
                    ->action(function (Translation $record, $data) {
                        $record->text = $data['text'];

                        $record->save();
                    }),
            ])
            ->filters([
                Filter::make('language')
                    ->label(__('Language'))
                    ->default(null)
                    ->schema([
                        Select::make('code')
                            ->nullable()
                            ->placeholder(__('Select language...'))
                            ->options(
                                Language::active()
                                    ->get()
                                    ->sort()
                                    ->groupBy(fn ($language) => Str::contains($language->code, '-') ? localized_country_name($language->code) : __('Worldwide'))
                                    ->mapWithKeys(fn ($languages, $countryName) => [
                                        $countryName => $languages->mapWithKeys(fn ($language) => [
                                            $language->code => Blade::render('<x-filament::icon :icon="country_flag(\'' . $language->languageCode . '\')" class="w-5" style="position: relative; top: -1px; margin-right: 3px; display: inline-block;" />') . localized_language_name($language->code) . ' (' . $countryName . ')',
                                        ])->toArray(),
                                    ])
                            )
                            ->preload()
                            ->multiple()
                            ->allowHtml()
                            ->label(__('Language'))
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['code'] ?? null, function ($query, $code) {
                            return $query->whereIn('code', $code);
                        });
                    }),

                Filter::make('translated_at')
                    ->label(__('Translated'))
                    ->default(null)
                    ->schema([
                        Select::make('translated_at')
                            ->nullable()
                            ->placeholder(__('Select...'))
                            ->default(null)
                            ->options([
                                'all' => __('All'),
                                'translated' => __('Translated'),
                                'not_translated' => __('Not Translated'),
                            ])
                            ->default('all')
                            ->label(__('Translated'))
                            ->native(false),
                    ])
                    ->query(function ($query, $data) {
                        if ($data['translated_at'] === 'all') {
                            return $query;
                        }

                        if ($data['translated_at'] === 'translated') {
                            return $query->whereNotNull('translated_at');
                        }

                        if ($data['translated_at'] === 'not_translated') {
                            return $query->whereNull('translated_at');
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTranslations::route('/'),
            'create' => CreateTranslation::route('/create'),
        ];
    }
}
