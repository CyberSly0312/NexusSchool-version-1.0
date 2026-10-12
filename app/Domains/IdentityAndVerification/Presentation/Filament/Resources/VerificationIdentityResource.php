<?php

declare(strict_types=1);

namespace App\Domains\IdentityAndVerification\Presentation\Filament\Resources;

use App\Domains\IdentityAndVerification\Application\Services\VerificationIdentityService;
use App\Domains\IdentityAndVerification\Domain\Enums\VerificationStatus;
use App\Domains\IdentityAndVerification\Domain\Exceptions\VerificationIdentityException;
use App\Domains\IdentityAndVerification\Infrastructure\Models\VerificationIdentity;
use App\Domains\IdentityAndVerification\Presentation\Filament\Resources\VerificationIdentityResource\Pages;
use App\Models\User;
use App\Traits\ScopedByTenant;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Read-only management surface for verification identities (SPR-016 Phase 4,
 * ADR-022 Decision 4).
 *
 * The surface lists the identities of the current school and exposes the two
 * sanctioned lifecycle transitions — revoke and replace — through the
 * application service. It never issues identities (issuance stays a
 * deferred/decisional concern), never displays the token digest, and delegates
 * every authorization decision to VerificationIdentityPolicy via the service's
 * document.revoke capability check. Records are always scoped to the current
 * tenant.
 */
class VerificationIdentityResource extends Resource
{
    use ScopedByTenant;

    protected static ?string $model = VerificationIdentity::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|\UnitEnum|null $navigationGroup = 'Vérification';

    protected static ?string $navigationLabel = 'Vérifications de documents';

    protected static ?string $modelLabel = 'Identité de vérification';

    protected static ?string $pluralModelLabel = 'Identités de vérification';

    protected static ?string $recordTitleAttribute = 'uuid';

    protected static ?int $navigationSort = 130;

    /**
     * Issuance is deliberately out of scope (SPR-016 Phase 6B is deferred):
     * the surface only manages identities already created by the issuance flow.
     */
    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('uuid')
                    ->label('Identifiant')
                    ->copyable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('academic_document_id')
                    ->label('Document')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn (VerificationStatus $state): string => $state->label())
                    ->color(fn (VerificationStatus $state): string => $state->color())
                    ->sortable(),
                Tables\Columns\TextColumn::make('issued_at')
                    ->label('Émis le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('revoked_at')
                    ->label('Révoqué le')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—')
                    ->sortable(),
                Tables\Columns\TextColumn::make('replaced_at')
                    ->label('Remplacé le')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make(),
                static::revokeAction(),
                static::replaceAction(),
            ]);
    }

    /**
     * Officially revoke an active verification identity (ADR-022 Decision 4).
     * Forward-only, irreversible and delegated to the application service,
     * which re-checks the document.revoke capability and the document's
     * publicly-verifiable state.
     */
    public static function revokeAction(): Action
    {
        return Action::make('revoke')
            ->label('Révoquer')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Révoquer l’identité de vérification')
            ->visible(fn (VerificationIdentity $record): bool => $record->isActive()
                && self::currentActor()?->can('revoke', $record) === true)
            ->schema([
                Textarea::make('reason')
                    ->label('Motif')
                    ->helperText('Motif officiel conservé dans le journal d’audit.')
                    ->required()
                    ->minLength(3)
                    ->maxLength(1000),
            ])
            ->action(function (VerificationIdentity $record, array $data): void {
                try {
                    app(VerificationIdentityService::class)->revoke(
                        $record->school_id,
                        $record->academic_document_id,
                        (string) ($data['reason'] ?? ''),
                        self::currentActor(),
                    );

                    Notification::make()
                        ->title('Identité de vérification révoquée')
                        ->success()
                        ->send();
                } catch (VerificationIdentityException $exception) {
                    Notification::make()
                        ->title('Révocation impossible')
                        ->body($exception->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }

    /**
     * Mark an active verification identity as replaced by a successor document
     * (ADR-022 Decision 4). The successor is validated by the application
     * service (same school, distinct document, publicly verifiable).
     */
    public static function replaceAction(): Action
    {
        return Action::make('replace')
            ->label('Remplacer')
            ->icon('heroicon-o-arrow-path')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading('Remplacer l’identité de vérification')
            ->visible(fn (VerificationIdentity $record): bool => $record->isActive()
                && self::currentActor()?->can('replace', $record) === true)
            ->schema([
                TextInput::make('successor_document_id')
                    ->label('Document de remplacement')
                    ->helperText('Identifiant du document successeur (même établissement).')
                    ->required(),
                Textarea::make('reason')
                    ->label('Motif')
                    ->required()
                    ->minLength(3)
                    ->maxLength(1000),
            ])
            ->action(function (VerificationIdentity $record, array $data): void {
                try {
                    app(VerificationIdentityService::class)->replaceWith(
                        $record->school_id,
                        $record->academic_document_id,
                        (string) ($data['successor_document_id'] ?? ''),
                        (string) ($data['reason'] ?? ''),
                        self::currentActor(),
                    );

                    Notification::make()
                        ->title('Identité de vérification remplacée')
                        ->success()
                        ->send();
                } catch (VerificationIdentityException $exception) {
                    Notification::make()
                        ->title('Remplacement impossible')
                        ->body($exception->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVerificationIdentities::route('/'),
            'view' => Pages\ViewVerificationIdentity::route('/{record}'),
        ];
    }

    private static function currentActor(): ?User
    {
        $user = auth()->user();

        return $user instanceof User ? $user : null;
    }
}
