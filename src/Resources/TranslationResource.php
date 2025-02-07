<?php

namespace Vormkracht10\FilamentTranslations\Resources;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\File;
use Vormkracht10\FilamentTranslations\Resources\TranslationResource\Pages;
use Vormkracht10\LaravelTranslations\Models\Translation;

class TranslationResource extends Resource
{
    protected static ?string $model = Translation::class;

    protected static ?string $slug = 'translations';

    public static function getNavigationIcon(): string
    {
        return 'heroicon-m-globe-alt';
    }

    public static function getNavigationParentItem(): ?string
    {
        if (filamentTranslations()->isUsingAppLang()) {

            return null;
        }

        return __('Languages');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Translations');
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
        static::checkLangConfig();

        return $table
            ->columns([
                Tables\Columns\IconColumn::make('locale')
                    ->label(__(''))
                    ->icon(fn ($record): string => getCountryFlag($record->locale))
                    ->color('danger')
                    ->size(fn () => Tables\Columns\IconColumn\IconColumnSize::TwoExtraLarge)
                    ->visible(fn () => ! filamentTranslations()->isUsingAppLang()),

                Tables\Columns\TextColumn::make('key')
                    ->label(__('Key'))
                    ->searchable()
                    ->description(fn ($record) => $record->group)
                    ->sortable(),

                Tables\Columns\TextInputColumn::make('text')
                    ->width('1/3')
                    ->label(__('Text'))
                    ->searchable()
                    ->sortable()
                    ->translated(),
            ])
            ->actions([
                EditAction::make()
                    ->modalHeading(__('Edit Translation'))
                    ->modalDescription(fn ($record) => $record->key)
                    ->modalIcon(fn ($record) => filamentTranslations()->isUsingAppLang() ? static::getNavigationIcon() : getCountryFlag($record->locale))
                    ->modalIconColor(null),
            ])
            ->filters([
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

    public static function checkLangConfig(): void
    {
        if (! filamentTranslations()->isUsingAppLang()) {
            return;
        }
        $appLocale = config('app.locale');

        $languages = LanguageResource::getModel()::where('locale', '!=', $appLocale)->get();

        $languages->each->delete();

        $translations = static::getModel()::where('locale', '!=', $appLocale)->get();

        $translations->each->delete();

        $language = LanguageResource::getModel()::where('locale', $appLocale);

        if (! $language->exists()) {
            $jsonPath = base_path('vendor/vormkracht10/filament-translations/resources/json/langCodes.json');

            if (! File::exists($jsonPath)) {
                throw new \Exception("Language codes file not found at path: {$jsonPath}");
            }

            $langCodesArray = File::json($jsonPath);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Failed to decode JSON from language codes file: ' . json_last_error_msg());
            }

            $nativeName = $langCodesArray[$appLocale]['nativeName'] ?? null;

            if (! $nativeName) {
                throw new \Exception("Language code for locale {$appLocale} not found in language codes file.");
            }

            $langLabel = explode(',', $nativeName)[0];

            if (! $langLabel) {
                throw new \Exception("Language label for locale {$appLocale} not found in language codes file.");
            }

            $language = $language->create([
                'locale' => $appLocale,
                'label' => $langLabel,
            ]);
        }
    }
}
