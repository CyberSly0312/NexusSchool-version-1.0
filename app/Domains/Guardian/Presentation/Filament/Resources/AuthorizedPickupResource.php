<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Presentation\Filament\Resources;

use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationship;
use App\Domains\Guardian\Presentation\Filament\Resources\AuthorizedPickupResource\Pages;
use App\Traits\ScopedByTenant;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AuthorizedPickupResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = StudentGuardianRelationship::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-key';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestion des tuteurs';

    protected static ?string $navigationLabel = 'Récupération autorisée';

    protected static ?string $modelLabel = 'Récupération autorisée';

    protected static ?string $pluralModelLabel = 'Récupérations autorisées';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 53;

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->modifyQueryUsing(fn ($query) => $query->authorizedPickups())
            ->columns([
                Tables\Columns\TextColumn::make('student.full_name')->label('Élève')->searchable(),
                Tables\Columns\TextColumn::make('guardian.full_name')->label('Personne autorisée')->searchable(),
                Tables\Columns\TextColumn::make('relationshipType.label')->label('Type'),
                Tables\Columns\TextColumn::make('pickup_valid_from')->label('Valide du')->date(),
                Tables\Columns\TextColumn::make('pickup_valid_until')->label('Valide jusqu\'au')->date(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('student_id')->label('Élève')
                    ->relationship('student', 'id'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuthorizedPickups::route('/'),
        ];
    }
}
