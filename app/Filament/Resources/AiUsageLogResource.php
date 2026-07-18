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

    protected static string|\UnitEnum|null $navigationGroup = 'AI Platform';

    protected static ?int $navigationSort = 75;

    protected static ?string $navigationLabel = 'AI Usage Logs';

    protected static bool $recordIsGloballySearchable = false;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Usage Details')
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

                SectionComponent::make('Token Usage')
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
                    ->money('USD')
                    ->sortable(),
                TextColumn::make('latency_ms')
                    ->label('Latency')
                    ->suffix('ms')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                IconColumn::make('was_cached')
                    ->boolean()
                    ->label('Cached')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('user.name')
                    ->label('User')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('provider.name')
                    ->label('Provider')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(AiUsageLogStatus::labels()),
                SelectFilter::make('action')
                    ->options([
                        'conversation_started' => 'Conversation Started',
                        'message_generated' => 'Message Generated',
                        'knowledge_indexed' => 'Knowledge Indexed',
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
