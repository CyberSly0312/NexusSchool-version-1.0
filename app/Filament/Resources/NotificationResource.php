<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\Communication\Enums\NotificationChannel;
use App\Domains\Communication\Enums\NotificationStatus;
use App\Filament\Resources\NotificationResource\Pages;
use App\Models\Notification;
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

class NotificationResource extends Resource
{
    protected static ?string $model = Notification::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bell';

    protected static string|\UnitEnum|null $navigationGroup = 'Communication';

    protected static ?string $navigationLabel = 'Notifications';

    protected static ?string $modelLabel = 'Notification';

    protected static ?string $pluralModelLabel = 'Notifications';

    protected static ?int $navigationSort = 60;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Détails de la notification')
                    ->schema([
                        Forms\Components\TextInput::make('type')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('body')
                            ->required()
                            ->rows(4),
                    ])
                    ->columns(1),

                SectionComponent::make('Canal et statut')
                    ->schema([
                        Forms\Components\Select::make('channel')
                            ->options(NotificationChannel::labels())
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options(NotificationStatus::labels())
                            ->required(),
                        Forms\Components\DateTimePicker::make('scheduled_at'),
                    ])
                    ->columns(3),

                SectionComponent::make('Metadata')
                    ->schema([
                        Forms\Components\KeyValue::make('data')
                            ->label('Données supplémentaires'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('channel')
                    ->badge()
                    ->color(fn (NotificationChannel $state): string => NotificationChannel::colors()[$state] ?? 'gray')
                    ->icon(fn (NotificationChannel $state): string => NotificationChannel::icons()[$state] ?? 'heroicon-o-question-mark'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (NotificationStatus $state): string => NotificationStatus::colors()[$state] ?? 'gray')
                    ->icon(fn (NotificationStatus $state): string => NotificationStatus::icons()[$state] ?? 'heroicon-o-question-mark'),
                TextColumn::make('sender.first_name')
                    ->label('Expéditeur')
                    ->toggleable(),
                TextColumn::make('sent_at')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(NotificationStatus::labels()),
                SelectFilter::make('channel')
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
            'index' => Pages\ListNotifications::route('/'),
            'create' => Pages\CreateNotification::route('/create'),
            'edit' => Pages\EditNotification::route('/{record}/edit'),
        ];
    }
}
