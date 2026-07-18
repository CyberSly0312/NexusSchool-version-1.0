<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Ai\Enums\AiConversationStatus;
use App\Filament\Resources\AiConversationResource\Pages;
use App\Models\AiConversation;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section as SectionComponent;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AiConversationResource extends Resource
{
    protected static ?string $model = AiConversation::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string|\UnitEnum|null $navigationGroup = 'AI Platform';

    protected static ?int $navigationSort = 71;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Conversation Details')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->maxLength(255),
                        Forms\Components\Select::make('status')
                            ->options(AiConversationStatus::labels())
                            ->default('active'),
                        Forms\Components\TextInput::make('model')
                            ->maxLength(255),
                    ])
                    ->columns(3),

                SectionComponent::make('Context')
                    ->schema([
                        Forms\Components\TextInput::make('context_type')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('context_id')
                            ->maxLength(36),
                        Forms\Components\Textarea::make('system_prompt')
                            ->rows(4),
                    ])
                    ->columns(1),

                SectionComponent::make('Summary')
                    ->schema([
                        Forms\Components\Textarea::make('summary')
                            ->rows(4),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (AiConversationStatus $state): string => $state->color()),
                TextColumn::make('user.first_name')
                    ->label('User')
                    ->toggleable(),
                TextColumn::make('provider.name')
                    ->label('Provider')
                    ->toggleable(),
                TextColumn::make('model')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('message_count')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('total_input_tokens')
                    ->label('Input Tokens')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('total_output_tokens')
                    ->label('Output Tokens')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(AiConversationStatus::labels()),
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
            'index' => Pages\ListAiConversations::route('/'),
            'create' => Pages\CreateAiConversation::route('/create'),
            'edit' => Pages\EditAiConversation::route('/{record}/edit'),
        ];
    }
}
