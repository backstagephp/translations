<?php

namespace Vormkracht10\FilamentTranslations\Resources;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Guava\FilamentIconSelectColumn\Tables\Columns\IconSelectColumn;
use Vormkracht10\FilamentTranslations\Models\Translation;
use Vormkracht10\FilamentTranslations\Resources\TranslationResource\Pages;

class TranslationResource extends Resource
{
    protected static ?string $model = Translation::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationGroup(): ?string
    {
        return __('Translations');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                IconSelectColumn::make('locale')
                    ->label(__('Locale'))
                    ->options(fn () => LanguageResource::getModel()::pluck('locale', 'locale')->toArray())
                    ->icons(fn () => LanguageResource::getModel()::pluck('locale', 'locale')->map(fn ($locale) => getCountryFlag($locale))->merge([null => ''])->toArray())
                    ->tooltip(fn ($record) => $record->locale)
                    ->searchable()
                    ->sortable()
                    ->closeOnSelection()
                    ->size(Tables\Columns\IconColumn\IconColumnSize::TwoExtraLarge),

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
                    ->sortable(),

                Tables\Columns\TextInputColumn::make('namespace')
                    ->label(__('Namespace'))
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListTranslations::route('/'),
            'create' => Pages\CreateTranslation::route('/create'),
            // 'edit' => Pages\EditTranslation::route('/{record}/edit'),
        ];
    }
}
