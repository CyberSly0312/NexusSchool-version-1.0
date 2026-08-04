<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Presentation\Filament\Resources;

use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationship;
use App\Domains\Guardian\Presentation\Filament\Resources\EmergencyContactsResource\Pages;
use App\Traits\ScopedByTenant;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmergencyContactsResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = StudentGuardianRelationship::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-lifebuoy';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestion des tuteurs';

    protected static ?string $navigationLabel = 'Contacts d\'urgence';

    protected static ?string $modelLabel = 'Contact d\'urgence';

    protected static ?string $pluralModelLabel = 'Contacts d\'urgence';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 52;

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->modifyQueryUsing(fn ($query) => $query->emergencyContacts())
            ->columns([
                Tables\Columns\TextColumn::make('student.full_name')->label('Élève')->searchable(),
                Tables\Columns\TextColumn::make('guardian.full_name')->label('Contact')->searchable(),
                Tables\Columns\TextColumn::make('relationshipType.label')->label('Type'),
                Tables\Columns\TextColumn::make('emergency_contact_priority')->label('Priorité')->sortable(),
                Tables\Columns\TextColumn::make('phone')->label('Téléphone'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('student_id')->label('Élève')
                    ->relationship('student', 'id'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmergencyContacts::route('/'),
        ];
    }
}
