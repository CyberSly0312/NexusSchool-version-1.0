<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Ai\Enums\AiMessageRole;
use App\Filament\Resources\AiMessageResource\Pages;
use App\Models\AiMessage;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section as SectionComponent;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AiMessageResource extends Resource
{
    protected static ?string $model = AiMessage::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-paper-airplane';

    protected static string|\UnitEnum|null $navigationGroup = 'AI Platform';

    protected static ?int $navigationSort = 72;

    protected static ?string $navigationLabel = 'AI Messages';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Message Details')
                    ->schema([
                        Forms\Components\Select::make('role')
                            ->options(AiMessageRole::labels())
                            ->required(),
                        Forms\Components\Textarea::make('content')
                            ->required()
                            ->rows(6),
                        Forms\Components\TextInput::make('model')
                            ->maxLength(255),
                    ])
                    ->columns(1),

                SectionComponent::make('Token Usage')
                    ->schema([
                        Forms\Components\TextInput::make('input_tokens')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('output_tokens')
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('finish_reason')
                            ->maxLength(50),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('role')
                    ->badge()
                    ->color(fn (AiMessageRole $state): string => $state->color()),
                TextColumn::make('content')
                    ->limit(80)
                    ->searchable(),
                TextColumn::make('conversation.title')
                    ->label('Conversation')
                    ->toggleable(),
                TextColumn::make('model')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('input_tokens')
                    ->label('In')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('output_tokens')
                    ->label('Out')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->options(AiMessageRole::labels()),
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
            'index' => Pages\ListAiMessages::route('/'),
        ];
    }
}
