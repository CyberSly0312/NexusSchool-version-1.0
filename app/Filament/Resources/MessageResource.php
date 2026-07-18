<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Communication\Enums\MessageStatus;
use App\Domains\Communication\Enums\MessageType;
use App\Filament\Resources\MessageResource\Pages;
use App\Models\Message;
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

class MessageResource extends Resource
{
    protected static ?string $model = Message::class;

    protected static ?string $recordTitleAttribute = 'subject';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string|\UnitEnum|null $navigationGroup = 'Communication';

    protected static ?int $navigationSort = 63;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Message Details')
                    ->schema([
                        Forms\Components\TextInput::make('subject')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('body')
                            ->required()
                            ->rows(4),
                    ]),

                SectionComponent::make('Type & Status')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->options(MessageType::labels())
                            ->required()
                            ->default('direct'),
                        Forms\Components\Select::make('status')
                            ->options(MessageStatus::labels())
                            ->required()
                            ->default('draft'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subject')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('body')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (MessageType $state): string => MessageType::colors()[$state] ?? 'gray')
                    ->icon(fn (MessageType $state): string => MessageType::icons()[$state] ?? 'heroicon-o-question-mark'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (MessageStatus $state): string => MessageStatus::colors()[$state] ?? 'gray')
                    ->icon(fn (MessageStatus $state): string => MessageStatus::icons()[$state] ?? 'heroicon-o-question-mark'),
                TextColumn::make('sender.first_name')
                    ->label('Sender')
                    ->toggleable(),
                TextColumn::make('sent_at')
                    ->dateTime()
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
                SelectFilter::make('type')
                    ->options(MessageType::labels()),
                SelectFilter::make('status')
                    ->options(MessageStatus::labels()),
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
            'index' => Pages\ListMessages::route('/'),
            'create' => Pages\CreateMessage::route('/create'),
            'edit' => Pages\EditMessage::route('/{record}/edit'),
        ];
    }
}
