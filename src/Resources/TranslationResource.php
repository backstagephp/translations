<?php

namespace Vormkracht10\FilamentTranslations\Resources;

use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Vormkracht10\FilamentTranslations\Resources\TranslationResource\Pages;
use Vormkracht10\LaravelTranslations\Models\Translation;

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
                    ->icon(fn ($record): string => getCountryFlag($record->locale))
                    ->color('danger')
                    ->size(fn () => Tables\Columns\IconColumn\IconColumnSize::TwoExtraLarge),

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
