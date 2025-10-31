<?php

namespace Backstage\Translations\Filament\Resources\LanguageResource\RelationManagers;

use Backstage\Translations\Laravel\Enums\LanguageRuleConditionType;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LanguageRulesRelationManager extends RelationManager
{
    protected static string $relationship = 'languageRules';

    protected static ?string $title = 'Language Rules';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('name'),

                Repeater::make('conditions')
                    ->relationship('conditions')
                    ->table([
                        TableColumn::make('key'),
                        TableColumn::make('type'),
                        TableColumn::make('value'),
                    ])
                    ->compact()
                    ->schema([
                        TextInput::make('key'),

                        Select::make('type')
                            ->options(LanguageRuleConditionType::class),

                        TextInput::make('value'),
                    ])
                    ->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ])
            ->columns([
                TextColumn::make('name'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
