<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Presentation\Filament\Resources\EnrollmentResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TransfersRelationManager extends RelationManager
{
    protected static string $relationship = 'transfers';

    protected static ?string $title = 'Transferts';

    protected static ?string $recordTitleAttribute = 'id';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('transfer_type')
                    ->label('Type')
                    ->badge(),
                Tables\Columns\TextColumn::make('fromClass.name')
                    ->label('Classe d\'origine'),
                Tables\Columns\TextColumn::make('toClass.name')
                    ->label('Classe cible'),
                Tables\Columns\TextColumn::make('to_school_id')
                    ->label('École cible'),
                Tables\Columns\TextColumn::make('reason')
                    ->label('Motif'),
                Tables\Columns\TextColumn::make('transfer_date')
                    ->label('Le')
                    ->date()
                    ->sortable(),
            ])
            ->defaultSort('transfer_date', 'desc');
    }
}
