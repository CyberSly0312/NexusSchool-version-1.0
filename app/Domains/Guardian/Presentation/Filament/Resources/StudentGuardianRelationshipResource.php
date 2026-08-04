<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Presentation\Filament\Resources;

use App\Domains\Guardian\Infrastructure\Models\RelationshipType;
use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationship;
use App\Domains\Guardian\Presentation\Filament\Resources\StudentGuardianRelationshipResource\Pages;
use App\Domains\Guardian\Presentation\Filament\Resources\StudentGuardianRelationshipResource\RelationManagers\HistoryRelationManager;
use App\Traits\ScopedByTenant;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class StudentGuardianRelationshipResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = StudentGuardianRelationship::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-link';

    protected static string|\UnitEnum|null $navigationGroup = 'Gestion des tuteurs';

    protected static ?string $navigationLabel = 'Relations';

    protected static ?string $modelLabel = 'Relation';

    protected static ?string $pluralModelLabel = 'Relations';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 51;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Relation')
                    ->schema([
                        Forms\Components\Select::make('relationship_type_id')->label('Type de relation')
                            ->options(fn (): array => RelationshipType::query()
                                ->forSchool((string) tenant()->id)
                                ->active()
                                ->pluck('label', 'id')
                                ->all())
                            ->required(),
                        Forms\Components\DatePicker::make('relationship_start_date')->label('Début de relation'),
                        Forms\Components\DatePicker::make('relationship_end_date')->label('Fin de relation'),
                    ])->columns(3),
                Section::make('Rôles')
                    ->schema([
                        Forms\Components\Toggle::make('is_primary')->label('Tuteur principal'),
                        Forms\Components\Toggle::make('is_emergency_contact')->label('Contact d\'urgence'),
                        Forms\Components\Toggle::make('is_authorized_pickup')->label('Récupération autorisée'),
                        Forms\Components\Toggle::make('is_financial_responsible')->label('Responsable financier'),
                        Forms\Components\Toggle::make('is_notification_recipient')->label('Destinataire des notifications'),
                        Forms\Components\Toggle::make('is_medical_contact')->label('Contact médical'),
                    ])->columns(3),
                Section::make('Contact d\'urgence & récupération')
                    ->schema([
                        Forms\Components\TextInput::make('emergency_contact_priority')->label('Priorité d\'urgence')
                            ->numeric()->minValue(1)->maxValue(255),
                        Forms\Components\DatePicker::make('pickup_valid_from')->label('Récupération valide du'),
                        Forms\Components\DatePicker::make('pickup_valid_until')->label('Récupération valide jusqu\'au'),
                    ])->columns(3),
                Section::make('Notes')
                    ->schema([
                        Forms\Components\Textarea::make('notes')->label('Notes')->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('student.full_name')->label('Élève')
                    ->searchable(['student.first_name', 'student.last_name']),
                Tables\Columns\TextColumn::make('guardian.full_name')->label('Tuteur')
                    ->searchable(['guardian.first_name', 'guardian.last_name']),
                Tables\Columns\TextColumn::make('relationshipType.label')->label('Type')->sortable(),
                Tables\Columns\IconColumn::make('is_primary')->label('Principal')->boolean(),
                Tables\Columns\IconColumn::make('is_emergency_contact')->label('Urgence')->boolean(),
                Tables\Columns\IconColumn::make('is_authorized_pickup')->label('Récupération')->boolean(),
                Tables\Columns\TextColumn::make('relationship_start_date')->label('Depuis')->date()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('relationship_type_id')->label('Type')
                    ->options(fn (): array => RelationshipType::query()
                        ->forSchool((string) tenant()->id)
                        ->pluck('label', 'id')
                        ->all()),
                Tables\Filters\TrashedFilter::make()->label('Archivés'),
            ])
            ->actions([
                EditAction::make()->label('Modifier'),
                DeleteAction::make()->label('Supprimer'),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            HistoryRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudentGuardianRelationships::route('/'),
            'edit' => Pages\EditStudentGuardianRelationship::route('/{record}/edit'),
        ];
    }

    public static function canDelete($record = null): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }
}
