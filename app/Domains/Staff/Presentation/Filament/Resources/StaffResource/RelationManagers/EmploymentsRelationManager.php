<?php

declare(strict_types=1);

namespace App\Domains\Staff\Presentation\Filament\Resources\StaffResource\RelationManagers;

use App\Domains\Staff\Domain\Enums\EmploymentStatus;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class EmploymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'employments';

    protected static ?string $title = 'Contrats';

    protected static ?string $label = 'Contrat';

    protected static ?string $pluralLabel = 'Contrats';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employment_type')->label('Type')
                    ->badge()->color('info'),
                Tables\Columns\TextColumn::make('contract_type')->label('Contrat')
                    ->badge(),
                Tables\Columns\TextColumn::make('position.title')->label('Poste')->placeholder('—'),
                Tables\Columns\TextColumn::make('department.name')->label('Département')->placeholder('—'),
                Tables\Columns\TextColumn::make('contract_start_date')->label('Début')->date(),
                Tables\Columns\TextColumn::make('contract_end_date')->label('Fin')->date()->placeholder('—'),
                Tables\Columns\TextColumn::make('status')->label('Statut')
                    ->badge()
                    ->color(fn (EmploymentStatus $state): string => $state->color()),
            ])
            ->filters([])
            ->headerActions([])
            ->actions([]);
    }
}
