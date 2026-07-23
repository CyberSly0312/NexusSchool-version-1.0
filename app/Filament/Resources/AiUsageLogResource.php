<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Ai\Enums\AiUsageLogStatus;
use App\Filament\Resources\AiUsageLogResource\Pages;
use App\Models\AiUsageLog;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section as SectionComponent;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AiUsageLogResource extends Resource
{
    protected static ?string $model = AiUsageLog::class;

    protected static ?string $recordTitleAttribute = 'id';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static string|\UnitEnum|null $navigationGroup = 'Plateforme IA';

    protected static ?int $navigationSort = 75;

    protected static ?string $navigationLabel = 'Journaux d\'utilisation IA';

    protected static ?string $modelLabel = 'Journal d\'utilisation IA';

    protected static ?string $pluralModelLabel = 'Journaux d\'utilisation IA';

    protected static bool $recordIsGloballySearchable = false;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Détails d\'utilisation')
                    ->schema([
                        Forms\Components\TextInput::make('action')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('model')
                            ->maxLength(255),
                        Forms\Components\Select::make('status')
                            ->options(AiUsageLogStatus::labels())
                            ->default('success'),
                    ])
                    ->columns(3),

                SectionComponent::make('Utilisation des jetons')
                    ->schema([
                        Forms\Components\TextInput::make('input_tokens')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('output_tokens')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('estimated_cost')
                            ->numeric()
                            ->default(0)
                            ->prefix('$'),
                        Forms\Components\TextInput::make('latency_ms')
                            ->numeric()
                            ->nullable()
                            ->suffix('ms'),
                    ])
                    ->columns(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('action')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => AiUsageLogStatus::tryFrom($state)?->color() ?? 'gray'),
                TextColumn::make('model')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('total_tokens')
                    ->sortable(),
                TextColumn::make('estimated_cost')
                    ->money('FCFA', locale: 'fr_FR')
                    ->sortable(),
                TextColumn::make('latency_ms')
                    ->label('Latence')
                    ->suffix('ms')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                IconColumn::make('was_cached')
                    ->boolean()
                    ->label('En cache')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('user.name')
                    ->label('Utilisateur')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('provider.name')
                    ->label('Fournisseur')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(AiUsageLogStatus::labels()),
                SelectFilter::make('action')
                    ->options([
                        'conversation_started' => 'Conversation démarrée',
                        'message_generated' => 'Message généré',
                        'knowledge_indexed' => 'Connaissances indexées',
                    ]),
            ])
            ->actions([
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
            'index' => Pages\ListAiUsageLogs::route('/'),
        ];
    }
}
