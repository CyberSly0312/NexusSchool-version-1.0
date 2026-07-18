<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Communication\Enums\NotificationChannel;
use App\Filament\Resources\CommunicationChannelResource\Pages;
use App\Models\CommunicationChannel;
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

class CommunicationChannelResource extends Resource
{
    protected static ?string $model = CommunicationChannel::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-signal';

    protected static string|\UnitEnum|null $navigationGroup = 'Communication';

    protected static ?int $navigationSort = 64;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Channel Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('channel_type')
                            ->options(NotificationChannel::labels())
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                    ])
                    ->columns(3),

                SectionComponent::make('Configuration')
                    ->schema([
                        Forms\Components\TextInput::make('from_address')
                            ->label('From Address')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('from_name')
                            ->label('From Name')
                            ->maxLength(255),
                        Forms\Components\KeyValue::make('config')
                            ->label('Channel Config'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('channel_type')
                    ->badge()
                    ->color(fn (NotificationChannel $state): string => NotificationChannel::colors()[$state] ?? 'gray')
                    ->icon(fn (NotificationChannel $state): string => NotificationChannel::icons()[$state] ?? 'heroicon-o-question-mark'),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('from_address')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('from_name')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                SelectFilter::make('channel_type')
                    ->options(NotificationChannel::labels()),
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
            'index' => Pages\ListCommunicationChannels::route('/'),
            'create' => Pages\CreateCommunicationChannel::route('/create'),
            'edit' => Pages\EditCommunicationChannel::route('/{record}/edit'),
        ];
    }
}
