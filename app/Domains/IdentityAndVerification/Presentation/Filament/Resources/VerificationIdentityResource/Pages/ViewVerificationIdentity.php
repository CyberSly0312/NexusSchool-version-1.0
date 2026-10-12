<?php

declare(strict_types=1);

namespace App\Domains\IdentityAndVerification\Presentation\Filament\Resources\VerificationIdentityResource\Pages;

use App\Domains\IdentityAndVerification\Domain\Enums\VerificationStatus;
use App\Domains\IdentityAndVerification\Presentation\Filament\Resources\VerificationIdentityResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewVerificationIdentity extends ViewRecord
{
    protected static string $resource = VerificationIdentityResource::class;

    protected static ?string $title = 'Détail de la vérification';

    public function getHeaderActions(): array
    {
        return [
            VerificationIdentityResource::revokeAction(),
            VerificationIdentityResource::replaceAction(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Identité de vérification')
                    ->schema([
                        TextEntry::make('uuid')->label('Identifiant'),
                        TextEntry::make('academic_document_id')->label('Document'),
                        TextEntry::make('status')
                            ->label('Statut')
                            ->badge()
                            ->formatStateUsing(fn (VerificationStatus $state): string => $state->label())
                            ->color(fn (VerificationStatus $state): string => $state->color()),
                        TextEntry::make('issued_at')->label('Émis le')->dateTime('d/m/Y H:i'),
                    ])->columns(2),
                Section::make('Invalidation')
                    ->schema([
                        TextEntry::make('revocation_reason')->label('Motif')->placeholder('—'),
                        TextEntry::make('revoked_at')->label('Révoqué le')->dateTime('d/m/Y H:i')->placeholder('—'),
                        TextEntry::make('replaced_at')->label('Remplacé le')->dateTime('d/m/Y H:i')->placeholder('—'),
                        TextEntry::make('replaced_by_document_number')
                            ->label('Remplacé par')
                            ->placeholder('—'),
                    ])->columns(2),
            ]);
    }
}
