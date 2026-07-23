<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\System\Enums\HealthStatus;
use App\Filament\Resources\SystemHealthResource\Pages;
use App\Models\SystemHealth;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SystemHealthResource extends Resource
{
    protected static ?string $model = SystemHealth::class;

    protected static ?string $recordTitleAttribute = 'service';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-heart';

    protected static string|\UnitEnum|null $navigationGroup = 'Administration système';

    protected static ?int $navigationSort = 94;

    protected static ?string $navigationLabel = 'États du système';

    protected static ?string $modelLabel = 'État du système';

    protected static ?string $pluralModelLabel = 'États du système';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('service')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => HealthStatus::tryFrom($state)?->color() ?? 'gray'),
                TextColumn::make('response_time_ms')
                    ->label('Temps de réponse (ms)')
                    ->sortable(),
                TextColumn::make('message')
                    ->limit(60)
                    ->toggleable(),
                TextColumn::make('checked_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('checked_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(HealthStatus::labels()),
                SelectFilter::make('service')
                    ->options([
                        'database' => 'Base de données',
                        'cache' => 'Cache',
                        'queue' => 'File d\'attente',
                        'storage' => 'Stockage',
                        'mail' => 'Messagerie',
                        'scheduler' => 'Planificateur',
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSystemHealth::route('/'),
        ];
    }
}
