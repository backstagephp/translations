<?php

namespace Vormkracht10\FilamentTranslations\Resources;

use Filament\Actions\StaticAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Vormkracht10\FilamentTranslations\Resources\TranslationResource\Pages;
use Vormkracht10\LaravelTranslations\Models\Translation;

class TranslationResource extends Resource
{
    protected static ?string $model = Translation::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $slug = 'translations';

    public static function getNavigationGroup(): ?string
    {
        return __('Translations');
    }

    // public static function getLabel(): ?string
    // {
    //     return __('Translations');
    // }

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
                Tables\Columns\IconColumn::make('locale')
                    ->label(__(''))
                    ->icon(fn ($record): string => getCountryFlag($record->locale))
                    ->color('danger')
                    ->size(fn () => Tables\Columns\IconColumn\IconColumnSize::TwoExtraLarge),

                Tables\Columns\TextColumn::make('key')
                    ->label(__('Key'))
                    ->searchable()
                    ->description(fn($record) =>$record->group )
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
                ->modalDescription(fn($record) => $record->key)
                ->modalIcon(fn($record) => getCountryFlag($record->locale))
                ->modalIconColor(null)
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
