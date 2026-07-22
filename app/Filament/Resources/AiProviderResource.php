<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Ai\Enums\AiProvider;
use App\Filament\Resources\AiProviderResource\Pages;
use App\Models\AiProvider as AiProviderModel;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section as SectionComponent;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AiProviderResource extends Resource
{
    protected static ?string $model = AiProviderModel::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cpu-chip';

    protected static string|\UnitEnum|null $navigationGroup = 'Plateforme IA';

    protected static ?string $navigationLabel = 'Fournisseurs IA';

    protected static ?string $modelLabel = 'Fournisseur IA';

    protected static ?string $pluralModelLabel = 'Fournisseurs IA';

    protected static ?int $navigationSort = 70;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Provider Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('driver')
                            ->options(AiProvider::labels())
                            ->required()
                            ->reactive(),
                        Forms\Components\Textarea::make('description')
                            ->rows(3),
                    ])
                    ->columns(1),

                SectionComponent::make('Configuration')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                        Forms\Components\Select::make('default_model')
                            ->options(
                                fn ($get) => $get('driver') && AiProvider::tryFrom($get('driver'))
                                ? collect(config("ai.providers.{$get('driver')}.supported_models", []))
                                    ->mapWithKeys(fn ($v) => [$v => $v])
                                    ->toArray()
                                : []
                            ),
                        Forms\Components\KeyValue::make('supported_models')
                            ->label('Supported Models')
                            ->reorderable(),
                        Forms\Components\KeyValue::make('configuration')
                            ->label('Provider Configuration')
                            ->reorderable(),
                    ])
                    ->columns(2),

                SectionComponent::make('Rate Limits')
                    ->schema([
                        Forms\Components\TextInput::make('rate_limit_per_minute')
                            ->numeric()
                            ->default(60)
                            ->minValue(1)
                            ->maxValue(10000),
                        Forms\Components\TextInput::make('daily_token_limit')
                            ->numeric()
                            ->nullable(),
                        Forms\Components\TextInput::make('monthly_token_limit')
                            ->numeric()
                            ->nullable(),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('driver')
                    ->badge()
                    ->color(fn (string $state): string => AiProvider::tryFrom($state)?->color() ?? 'gray'),
                TextColumn::make('default_model')
                    ->searchable()
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('conversations_count')
                    ->counts('conversations')
                    ->label('Conversations')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('rate_limit_per_minute')
                    ->label('Rate Limit')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                SelectFilter::make('driver')
                    ->options(AiProvider::labels()),
                SelectFilter::make('is_active')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ]),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAiProviders::route('/'),
            'create' => Pages\CreateAiProvider::route('/create'),
            'edit' => Pages\EditAiProvider::route('/{record}/edit'),
        ];
    }
}
