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
use App\Traits\ScopedByTenant;

class MessageResource extends Resource
{
    use ScopedByTenant;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $model = Message::class;

    protected static ?string $recordTitleAttribute = 'subject';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string|\UnitEnum|null $navigationGroup = 'Communication';

    protected static ?string $navigationLabel = 'Messages';

    protected static ?string $modelLabel = 'Message';

    protected static ?string $pluralModelLabel = 'Messages';

    protected static ?int $navigationSort = 63;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Détails du message')
                    ->schema([
                        Forms\Components\TextInput::make('subject')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('body')
                            ->required()
                            ->rows(4),
                    ]),

                SectionComponent::make('Type et statut')
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
                    ->color(fn (MessageType $state): string => MessageType::colors()[$state->value] ?? 'gray')
                    ->icon(fn (MessageType $state): string => MessageType::icons()[$state->value] ?? 'heroicon-o-question-mark'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (MessageStatus $state): string => MessageStatus::colors()[$state->value] ?? 'gray')
                    ->icon(fn (MessageStatus $state): string => MessageStatus::icons()[$state->value] ?? 'heroicon-o-question-mark'),
                TextColumn::make('sender.first_name')
                    ->label('Expéditeur')
                    ->toggleable(),
                TextColumn::make('sent_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
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
