<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationChannelResource\Pages;
use App\Models\NotificationChannel;
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

class NotificationChannelResource extends Resource
{
    protected static ?string $model = NotificationChannel::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bell';

    protected static string|\UnitEnum|null $navigationGroup = 'System Administration';

    protected static ?int $navigationSort = 95;

    protected static ?string $navigationLabel = 'Notification Channels';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                SectionComponent::make('Channel Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(200),
                        Forms\Components\Select::make('type')
                            ->options([
                                'email' => 'Email',
                                'sms' => 'SMS',
                                'push' => 'Push Notification',
                                'webhook' => 'Webhook',
                                'database' => 'Database',
                            ])
                            ->required(),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                    ])
                    ->columns(3),

                SectionComponent::make('Configuration')
                    ->schema([
                        Forms\Components\KeyValue::make('configuration')
                            ->label('Channel Configuration')
                            ->reorderable(),
                        Forms\Components\KeyValue::make('conditions')
                            ->label('Conditions')
                            ->reorderable(),
                    ])
                    ->columns(2),

                SectionComponent::make('Description')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->rows(2),
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
                TextColumn::make('type')
                    ->badge(),
                IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('total_sent')
                    ->sortable()
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('last_used_at')
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
                    ->options([
                        'email' => 'Email',
                        'sms' => 'SMS',
                        'push' => 'Push',
                        'webhook' => 'Webhook',
                        'database' => 'Database',
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
            'index' => Pages\ListNotificationChannels::route('/'),
            'create' => Pages\CreateNotificationChannel::route('/create'),
            'edit' => Pages\EditNotificationChannel::route('/{record}/edit'),
        ];
    }
}
