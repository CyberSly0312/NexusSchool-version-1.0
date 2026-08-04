<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Presentation\Filament\Resources\StudentGuardianRelationshipResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class HistoryRelationManager extends RelationManager
{
    protected static string $relationship = 'history';

    protected static ?string $title = 'Historique';

    protected static ?string $recordTitleAttribute = 'description';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                Tables\Columns\TextColumn::make('event')->label('Événement')->badge(),
                Tables\Columns\TextColumn::make('description')->label('Description'),
                Tables\Columns\TextColumn::make('user.name')->label('Utilisateur'),
                Tables\Columns\TextColumn::make('occurred_at')->label('Date')->dateTime()->sortable(),
            ])
            ->defaultSort('occurred_at', 'desc');
    }
}
