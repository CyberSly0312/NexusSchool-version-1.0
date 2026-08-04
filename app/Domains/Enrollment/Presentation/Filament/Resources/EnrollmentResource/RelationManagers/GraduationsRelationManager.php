<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Presentation\Filament\Resources\EnrollmentResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class GraduationsRelationManager extends RelationManager
{
    protected static string $relationship = 'graduations';

    protected static ?string $title = 'Diplômes';

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
                Tables\Columns\TextColumn::make('educationLevel.name')
                    ->label('Niveau'),
                Tables\Columns\TextColumn::make('decision')
                    ->label('Décision')
                    ->badge(),
                Tables\Columns\TextColumn::make('graduated_on')
                    ->label('Le')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('certificate_reference')
                    ->label('Réf. certificat'),
            ])
            ->defaultSort('graduated_on', 'desc');
    }
}
