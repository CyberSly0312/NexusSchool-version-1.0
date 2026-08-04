<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Presentation\Filament\Resources\GuardianResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RelationshipsRelationManager extends RelationManager
{
    protected static string $relationship = 'relationships';

    protected static ?string $title = 'Relations';

    protected static ?string $recordTitleAttribute = 'name';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('student.full_name')->label('Élève')->searchable(),
                Tables\Columns\TextColumn::make('relationshipType.label')->label('Type'),
                Tables\Columns\IconColumn::make('is_primary')->label('Principal')->boolean(),
                Tables\Columns\IconColumn::make('is_emergency_contact')->label('Urgence')->boolean(),
                Tables\Columns\IconColumn::make('is_authorized_pickup')->label('Récupération')->boolean(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make()->label('Archivés'),
            ]);
    }
}
