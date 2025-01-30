<?php

namespace Vormkracht10\FilamentTranslations\Resources;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Enums\FiltersLayout;
use Vormkracht10\LaravelTranslations\Models\Translation;
use Vormkracht10\FilamentTranslations\Resources\TranslationResource\Pages;

class TranslationResource extends Resource
{
    protected static ?string $model = Translation::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string
    {
        return __('Translations');
    }

    public static function getLabel(): ?string
    {
        return __('Translations');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('locale')
                    ->label(__('Locale'))
                    ->icon(fn($record): string => getCountryFlag($record->locale))
                    ->color('danger')
                    ->size(fn() => Tables\Columns\IconColumn\IconColumnSize::TwoExtraLarge),

                Tables\Columns\TextInputColumn::make('group')
                    ->label(__('Group'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextInputColumn::make('key')
                    ->label(__('Key'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextInputColumn::make('text')
                    ->label(__('Text'))
                    ->searchable()
                    ->sortable()
                    ->translated(),

                Tables\Columns\TextInputColumn::make('namespace')
                    ->label(__('Namespace'))
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Filter::make('not_translated')
                    ->modifyQueryUsing(fn($query) => $query->whereNull('translated_at'))
                    ->toggle(),

                Filter::make('translated')
                    ->modifyQueryUsing(fn($query) => $query->whereNotNull('translated_at'))
                    ->toggle(),
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
