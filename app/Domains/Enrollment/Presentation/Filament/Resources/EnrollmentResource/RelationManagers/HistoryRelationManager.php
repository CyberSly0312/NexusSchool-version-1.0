<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Presentation\Filament\Resources\EnrollmentResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class HistoryRelationManager extends RelationManager
{
    protected static string $relationship = 'history';

    protected static ?string $title = 'Historique';

    protected static ?string $recordTitleAttribute = 'description';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                Tables\Columns\TextColumn::make('occurred_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('event')
                    ->label('Événement')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'promoted', 'repeated' => 'info',
                        'transferred' => 'warning',
                        'graduated' => 'primary',
                        'withdrawn', 'suspended' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('description')
                    ->label('Description'),
                Tables\Columns\TextColumn::make('user_type')
                    ->label('Par')
                    ->toggleable()
                    ->toggledHiddenByDefault(),
            ])
            ->defaultSort('occurred_at', 'desc');
    }
}
