<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Presentation\Filament\Resources\EnrollmentResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PromotionsRelationManager extends RelationManager
{
    protected static string $relationship = 'promotions';

    protected static ?string $title = 'Passages / Redoublements';

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
                Tables\Columns\TextColumn::make('decision')
                    ->label('Décision')
                    ->badge(),
                Tables\Columns\TextColumn::make('academicCalendarYear.name')
                    ->label('Année scolaire'),
                Tables\Columns\TextColumn::make('fromClass.name')
                    ->label('Classe d\'origine'),
                Tables\Columns\TextColumn::make('toClass.name')
                    ->label('Classe cible'),
                Tables\Columns\TextColumn::make('promoted_on')
                    ->label('Le')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('academic_result')
                    ->label('Résultat')
                    ->formatStateUsing(fn ($state): string => is_array($state) && isset($state['average'])
                        ? number_format((float) $state['average'], 2)
                        : '—'),
            ])
            ->defaultSort('promoted_on', 'desc');
    }
}
