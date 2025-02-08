<?php

namespace Backstage\Translations\Resources;

use Backstage\Translations\Laravel\Models\Translation;
use Backstage\Translations\Resources\TranslationResource\Pages;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;

class TranslationResource extends Resource
{
    protected static ?string $model = Translation::class;

    protected static ?string $slug = 'translations';

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
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('code')
                    ->label('')
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
