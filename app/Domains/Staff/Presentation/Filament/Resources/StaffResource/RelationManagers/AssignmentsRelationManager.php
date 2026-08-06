<?php

declare(strict_types=1);

namespace App\Domains\Staff\Presentation\Filament\Resources\StaffResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AssignmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'assignments';

    protected static ?string $title = 'Affectations';

    protected static ?string $label = 'Affectation';

    protected static ?string $pluralLabel = 'Affectations';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('assignment_type')->label('Type')
                    ->badge()->color('info'),
                Tables\Columns\TextColumn::make('department.name')->label('Département')->placeholder('—'),
                Tables\Columns\TextColumn::make('position.title')->label('Poste')->placeholder('—'),
                Tables\Columns\TextColumn::make('assignment_start_date')->label('Début')->date(),
                Tables\Columns\TextColumn::make('assignment_end_date')->label('Fin')->date()->placeholder('—'),
                Tables\Columns\IconColumn::make('is_current')->label('En cours')->boolean(),
            ])
            ->filters([])
            ->headerActions([])
            ->actions([]);
    }
}
