<?php

namespace Vormkracht10\FilamentTranslations\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Vormkracht10\FilamentTranslations\Models\Language;
use Vormkracht10\FilamentTranslations\Resources\LanguageResource\Pages;

class LanguageResource extends Resource
{
    protected static ?string $model = Language::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $slug = 'translations/languages';

    protected static bool $isScopedToTenant = false;

    public static function getNavigationParentItem(): ?string
    {
        return __('Translations');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('Translations');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('locale')
                    ->label(__('Locale'))
                    ->prefixIcon(fn ($state): ?string => $state ? getCountryFlag($state) : null)
                    ->unique(fn () => (new (static::getModel()))->getTable(), fn ($component) => $component->getName(), null, true)
                    ->live()
                    ->reactive()
                    ->required(),

                Forms\Components\TextInput::make('label')
                    ->label(__('Label'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('id')
                    ->label(__('Flag'))
                    ->icon(fn ($record): string => getCountryFlag($record->locale))
                    ->color('danger')
                    ->size(fn () => Tables\Columns\IconColumn\IconColumnSize::TwoExtraLarge),

                Tables\Columns\TextColumn::make('locale')
                    ->label(__('Locale')),

                Tables\Columns\TextColumn::make('label')
                    ->label(__('Label')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
