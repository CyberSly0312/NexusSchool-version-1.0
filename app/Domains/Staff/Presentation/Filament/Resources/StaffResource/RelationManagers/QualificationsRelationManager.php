<?php

declare(strict_types=1);

namespace App\Domains\Staff\Presentation\Filament\Resources\StaffResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class QualificationsRelationManager extends RelationManager
{
    protected static string $relationship = 'qualifications';

    protected static ?string $title = 'Diplômes & qualifications';

    protected static ?string $label = 'Qualification';

    protected static ?string $pluralLabel = 'Qualifications';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('qualification_level')->label('Niveau')
                    ->badge()->color('info'),
                Tables\Columns\TextColumn::make('field_of_study')->label('Domaine')->placeholder('—'),
                Tables\Columns\TextColumn::make('institution')->label('Institution'),
                Tables\Columns\TextColumn::make('qualification_date')->label('Date')->date(),
            ])
            ->filters([])
            ->headerActions([])
            ->actions([]);
    }
}
