<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Presentation\Filament\Resources;

use App\Domains\Guardian\Application\Actions\UnlinkGuardianAction;
use App\Domains\Guardian\Application\DTOs\GuardianRelationshipRevocationData;
use App\Domains\Guardian\Application\DTOs\GuardianRelationshipVerificationData;
use App\Domains\Guardian\Application\Services\GuardianRelationshipVerificationService;
use App\Domains\Guardian\Domain\Enums\GuardianAccessDenialReason;
use App\Domains\Guardian\Domain\Enums\RelationshipPortalAccessStatus;
use App\Domains\Guardian\Domain\Enums\RelationshipVerificationStatus;
use App\Domains\Guardian\Domain\Exceptions\GuardianRelationshipVerificationException;
use App\Domains\Guardian\Infrastructure\Models\RelationshipType;
use App\Domains\Guardian\Infrastructure\Models\StudentGuardianRelationship;
use App\Domains\Guardian\Presentation\Filament\Resources\StudentGuardianRelationshipResource\Pages;
use App\Domains\Guardian\Presentation\Filament\Resources\StudentGuardianRelationshipResource\RelationManagers\HistoryRelationManager;
use App\Models\User;
use App\Traits\ScopedByTenant;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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
                Action::make('request_portal_access')
                    ->label('Demander l’accès au portail')
                    ->visible(fn (StudentGuardianRelationship $record): bool => $record->verification_status === RelationshipVerificationStatus::Unverified
                        && $record->portal_access_status === RelationshipPortalAccessStatus::NotAuthorized
                        && Gate::allows('verify', $record))
                    ->action(function (StudentGuardianRelationship $record): void {
                        self::runRelationshipAction(
                            fn (GuardianRelationshipVerificationService $service): StudentGuardianRelationship => $service->request(
                                GuardianRelationshipVerificationData::fromRelationship($record),
                            ),
                            'Demande enregistrée',
                        );
                    }),
                Action::make('approve_portal_access')
                    ->label('Approuver l’accès au portail')
                    ->visible(fn (StudentGuardianRelationship $record): bool => $record->verification_status === RelationshipVerificationStatus::Unverified
                        && $record->portal_access_status === RelationshipPortalAccessStatus::NotAuthorized
                        && Gate::allows('verify', $record))
                    ->action(function (StudentGuardianRelationship $record): void {
                        self::runRelationshipAction(
                            fn (GuardianRelationshipVerificationService $service): StudentGuardianRelationship => $service->verifyAndAuthorize(
                                GuardianRelationshipVerificationData::fromRelationship($record),
                            ),
                            'Accès au portail approuvé',
                        );
                    }),
                Action::make('reject_portal_access')
                    ->label('Refuser l’accès au portail')
                    ->visible(fn (StudentGuardianRelationship $record): bool => $record->verification_status === RelationshipVerificationStatus::Unverified
                        && $record->portal_access_status === RelationshipPortalAccessStatus::NotAuthorized
                        && Gate::allows('verify', $record))
                    ->action(function (StudentGuardianRelationship $record): void {
                        self::runRelationshipAction(
                            fn (GuardianRelationshipVerificationService $service): StudentGuardianRelationship => $service->reject(
                                GuardianRelationshipVerificationData::fromRelationship($record),
                            ),
                            'Demande refusée',
                        );
                    }),
                Action::make('revoke_portal_access')
                    ->label('Révoquer l’accès au portail')
                    ->visible(fn (StudentGuardianRelationship $record): bool => $record->portal_access_status === RelationshipPortalAccessStatus::Authorized
                        && Gate::allows('revoke', $record))
                    ->action(function (StudentGuardianRelationship $record): void {
                        self::runRelationshipAction(
                            fn (GuardianRelationshipVerificationService $service): StudentGuardianRelationship => $service->revoke(
                                GuardianRelationshipRevocationData::fromRelationship(
                                    $record,
                                    GuardianAccessDenialReason::RelationshipRevoked,
                                ),
                            ),
                            'Accès au portail révoqué',
                        );
                    }),
                EditAction::make()->label('Modifier'),
                Action::make('unlink_relationship')
                    ->label('Supprimer')
                    ->visible(fn (StudentGuardianRelationship $record): bool => self::canDelete($record))
                    ->action(function (StudentGuardianRelationship $record): void {
                        $actor = Auth::user();

                        if (! $actor instanceof User || ! self::canDelete($record)) {
                            abort(403);
                        }

                        app(UnlinkGuardianAction::class)->execute(
                            $record,
                            'Relation supprimée via l’administration',
                            $actor,
                        );
                    }),
            ])
            ->bulkActions([]);
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

    /**
     * @param  callable(GuardianRelationshipVerificationService): StudentGuardianRelationship  $operation
     */
    private static function runRelationshipAction(callable $operation, string $successTitle): void
    {
        try {
            $operation(app(GuardianRelationshipVerificationService::class));

            Notification::make()
                ->title($successTitle)
                ->success()
                ->send();
        } catch (GuardianRelationshipVerificationException) {
            Notification::make()
                ->title('Action refusée')
                ->body('L’acteur ou la preuve de relation n’est pas autorisé.')
                ->danger()
                ->send();
        }
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
