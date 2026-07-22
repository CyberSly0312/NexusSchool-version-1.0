<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Domains\System\Enums\AuditAction;
use App\Filament\Resources\AuditLogResource\Pages;
use App\Models\AuditLog;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static ?string $recordTitleAttribute = 'id';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|\UnitEnum|null $navigationGroup = 'Administration système';

    protected static ?string $navigationLabel = 'Journaux d\'audit';

    protected static ?string $modelLabel = 'Journal d\'audit';

    protected static ?string $pluralModelLabel = 'Journaux d\'audit';

    protected static ?int $navigationSort = 91;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('action')
                    ->badge()
                    ->color(fn (string $state): string => AuditAction::tryFrom($state)?->color() ?? 'gray'),
                TextColumn::make('auditable_type')
                    ->label('Type')
                    ->limit(30)
                    ->toggleable(),
                TextColumn::make('auditable_id')
                    ->label('ID')
                    ->limit(10)
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('description')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('event')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
                TextColumn::make('user_id')
                    ->label('User')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('action')
                    ->options(AuditAction::labels()),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditLogs::route('/'),
        ];
    }
}
