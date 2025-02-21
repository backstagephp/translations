<?php

namespace Backstage\Translations\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Backstage\Models\Language;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Database\Eloquent\Builder;
use Backstage\Translations\Laravel\Models\Translation;
use Backstage\Translations\Filament\Resources\TranslationResource\Pages;

class TranslationResource extends Resource
{
    protected static ?string $model = Translation::class;

    protected static ?string $slug = 'translations';

    protected static bool $isScopedToTenant = false;

    public static function getNavigationParentItem(): ?string
    {
        return __('Languages');
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-language';
    }

    public static function getNavigationGroup(): ?string
    {
        return LanguageResource::getNavigationGroup();
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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
            ->columns([
                Tables\Columns\IconColumn::make('code')
                    ->label('')
                    ->sortable()
                    ->icon(fn ($record): string => getCountryFlag($record->languageCode))
                    ->color('danger')
                    ->size(fn () => Tables\Columns\IconColumn\IconColumnSize::TwoExtraLarge),

                Tables\Columns\TextColumn::make('key')
                    ->label(__('Key'))
                    ->searchable()
                    ->width('50%')
                    ->limit(50)
                    ->description(fn ($record) => $record->group)
                    ->sortable(),

                Tables\Columns\TextInputColumn::make('text')
                    ->label(__('Text'))
                    ->searchable()
                    ->width('50%')
                    ->sortable()
                    ->translated(),
            ])
            ->actions([
                EditAction::make()
                    ->modalHeading(__('Edit Translation'))
                    ->modalDescription(fn ($record) => $record->key)
                    ->modalIcon(fn ($record) => getCountryFlag($record->languageCode))
                    ->modalIconColor(null),
            ])
            ->filters([
                Filter::make('language')
                    ->label(__('Language'))
                    ->default(null)
                    ->form([
                        Select::make('code')
                            ->nullable()
                            ->placeholder(__('Select language...'))
                            ->options(
                                Language::active()
                                ->get()
                                ->sort()
                                ->groupBy(function ($language) {
                                    return Str::contains($language->code, '-') ? getLocalizedCountryName($language->code) : __('Worldwide');
                                })
                                ->mapWithKeys(fn ($languages, $countryName) => [
                                    $countryName => $languages->mapWithKeys(fn ($language) => [
                                        $language->code => '<img src="data:image/svg+xml;base64,' . base64_encode(file_get_contents(base_path('vendor/backstage/cms/resources/img/flags/' . explode('-', $language->code)[0] . '.svg'))) . '" class="inline-block relative w-5" style="top: -1px; margin-right: 3px;"> ' . getLocalizedLanguageName($language->code) . ' (' .$countryName. ')',
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
                    ->form([
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
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTranslations::route('/'),
            'create' => Pages\CreateTranslation::route('/create'),
        ];
    }
}
