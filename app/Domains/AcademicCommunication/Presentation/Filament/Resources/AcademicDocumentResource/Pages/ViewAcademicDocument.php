<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Presentation\Filament\Resources\AcademicDocumentResource\Pages;

use App\Domains\AcademicCommunication\Application\AcademicCommunicationService;
use App\Domains\AcademicCommunication\Application\DTOs\ApproveDocumentData;
use App\Domains\AcademicCommunication\Application\DTOs\ArchiveDocumentData;
use App\Domains\AcademicCommunication\Application\DTOs\PublishDocumentData;
use App\Domains\AcademicCommunication\Application\DTOs\ReviewDocumentData;
use App\Domains\AcademicCommunication\Domain\Enums\ApprovalStatus;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentStatus;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Domains\AcademicCommunication\Infrastructure\Models\DocumentApproval;
use App\Domains\AcademicCommunication\Infrastructure\Models\PublicationPolicy;
use App\Domains\AcademicCommunication\Presentation\Filament\Resources\AcademicDocumentResource;
use App\Domains\IdentityAndVerification\Domain\Enums\VerificationStatus;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ViewAcademicDocument extends ViewRecord
{
    protected static string $resource = AcademicDocumentResource::class;

    protected static ?string $title = 'Détail du document';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('review')
                ->label('Réviser')
                ->icon('heroicon-o-eye')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Réviser le document')
                ->visible(fn (): bool => $this->canPerform('review')
                    && $this->document()->canTransitionTo(DocumentStatus::Reviewed))
                ->action(function (): void {
                    try {
                        $document = $this->document();

                        app(AcademicCommunicationService::class)->review(
                            ReviewDocumentData::fromArray([
                                'school_id' => $document->school_id,
                                'document_id' => $document->getKey(),
                            ]),
                            auth()->user(),
                        );

                        $document->refresh();

                        Notification::make()->title('Document révisé')->success()->send();
                    } catch (\Throwable $exception) {
                        Notification::make()
                            ->title('Révision impossible')
                            ->body($exception->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Action::make('approve')
                ->label('Approuver')
                ->icon('heroicon-o-check-badge')
                ->color('primary')
                ->modalHeading('Approuver le document')
                ->modalDescription('Sélectionnez le rôle d\'approbation au titre duquel cette décision est rendue.')
                ->visible(fn (): bool => $this->canPerform('approve')
                    && $this->document()->requiresApproval()
                    && $this->document()->canTransitionTo(DocumentStatus::Approved))
                ->schema([
                    Select::make('approver_role')
                        ->label('Rôle d\'approbation')
                        ->options(fn (): array => $this->approvalRoleOptions())
                        ->required()
                        ->native(false),
                ])
                ->action(function (array $data): void {
                    try {
                        app(AcademicCommunicationService::class)->approve(
                            ApproveDocumentData::fromArray([
                                'school_id' => $this->document()->school_id,
                                'document_id' => $this->document()->getKey(),
                                'approver_role' => $data['approver_role'],
                                'approved' => true,
                            ]),
                            auth()->user(),
                        );

                        $this->document()->refresh();

                        Notification::make()->title('Document approuvé')->success()->send();
                    } catch (\Throwable $exception) {
                        Notification::make()
                            ->title('Approbation impossible')
                            ->body($exception->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Action::make('publish')
                ->label('Publier')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Publier le document')
                ->modalDescription('Le document sera diffusé via les canaux activés définis par sa politique de publication.')
                ->visible(fn (): bool => $this->canPerform('publish')
                    && $this->document()->canTransitionTo(DocumentStatus::Published))
                ->action(function (): void {
                    $document = $this->document();

                    try {
                        app(AcademicCommunicationService::class)->publish(
                            PublishDocumentData::fromArray([
                                'school_id' => $document->school_id,
                                'document_id' => $document->getKey(),
                            ]),
                            auth()->user(),
                        );

                        $document->refresh();

                        Notification::make()->title('Document publié')->success()->send();
                    } catch (\Throwable $exception) {
                        Notification::make()
                            ->title('Publication impossible')
                            ->body($exception->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Action::make('archive')
                ->label('Archiver')
                ->icon('heroicon-o-archive-box')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Archiver le document')
                ->visible(fn (): bool => $this->canPerform('archive')
                    && $this->document()->canTransitionTo(DocumentStatus::Archived))
                ->action(function (): void {
                    try {
                        $document = $this->document();

                        app(AcademicCommunicationService::class)->archive(
                            ArchiveDocumentData::fromArray([
                                'school_id' => $document->school_id,
                                'document_id' => $document->getKey(),
                            ]),
                            auth()->user(),
                        );

                        $document->refresh();

                        Notification::make()->title('Document archivé')->success()->send();
                    } catch (\Throwable $exception) {
                        Notification::make()
                            ->title('Archivage impossible')
                            ->body($exception->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    private function canPerform(string $capability): bool
    {
        $actor = auth()->user();

        return $actor !== null && $actor->can($capability, $this->document());
    }

    private function document(): AcademicDocument
    {
        $record = $this->getRecord();

        if (! $record instanceof AcademicDocument) {
            throw new \RuntimeException('The AcademicDocument view page expects an AcademicDocument record.');
        }

        return $record;
    }

    /**
     * Approval roles allowed by the document's publication policy.
     *
     * @return array<string, string>
     */
    private function approvalRoleOptions(): array
    {
        $policy = $this->document()->publicationPolicy;

        if (! $policy instanceof PublicationPolicy) {
            return [];
        }

        $roles = $policy->getAttribute('approval_roles');

        if (! is_array($roles)) {
            return [];
        }

        $options = [];

        foreach ($roles as $role) {
            $options[(string) $role] = Str::headline((string) $role);
        }

        return $options;
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextEntry::make('document_number')->label('N° de document'),
                TextEntry::make('title')->label('Titre'),
                TextEntry::make('document_type')
                    ->label('Type')
                    ->badge(),
                TextEntry::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn ($record): string => $record->status->color()),
                TextEntry::make('content_version')->label('Version'),
                TextEntry::make('approval_status')
                    ->label('Approbation')
                    ->badge()
                    ->formatStateUsing(fn (?ApprovalStatus $state): string => $state?->label() ?? '—')
                    ->color(fn (?ApprovalStatus $state): ?string => $state?->color()),
                Section::make('Historique d\'approbation')
                    ->description('Décisions d\'approbation enregistrées pour ce document (lecture seule).')
                    ->schema([
                        RepeatableEntry::make('approvals')
                            ->hiddenLabel()
                            ->placeholder('Aucune décision enregistrée.')
                            ->state(fn (AcademicDocument $record): Collection => $record->approvals()
                                ->where('school_id', $record->school_id)
                                ->with('approver')
                                ->orderBy('created_at')
                                ->get())
                            ->schema([
                                TextEntry::make('approver_role')
                                    ->label('Rôle')
                                    ->formatStateUsing(fn (string $state): string => Str::headline($state)),
                                TextEntry::make('status')
                                    ->label('Décision')
                                    ->badge()
                                    ->formatStateUsing(fn (?ApprovalStatus $state): string => $state?->label() ?? '—')
                                    ->color(fn (?ApprovalStatus $state): ?string => $state?->color()),
                                TextEntry::make('approver_name')
                                    ->label('Approuvé par')
                                    ->state(function (DocumentApproval $record): ?string {
                                        $approver = $record->approver;

                                        return $approver instanceof User ? $approver->full_name : null;
                                    })
                                    ->placeholder('—'),
                                TextEntry::make('comment')
                                    ->label('Commentaire')
                                    ->placeholder('—'),
                                TextEntry::make('decided_at')
                                    ->label('Décidé le')
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('—'),
                            ])->columns(2),
                    ]),
                Section::make('Vérification')
                    ->description('Identité de vérification émise pour ce document (lecture seule).')
                    ->schema([
                        TextEntry::make('verificationIdentity.status')
                            ->label('État')
                            ->badge()
                            ->placeholder('Aucune')
                            ->formatStateUsing(fn (?VerificationStatus $state): string => $state?->label() ?? 'Aucune')
                            ->color(fn (?VerificationStatus $state): ?string => $state?->color()),
                        TextEntry::make('verificationIdentity.issued_at')
                            ->label('Émise le')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('—'),
                        TextEntry::make('verificationIdentity.revoked_at')
                            ->label('Révoquée le')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('—'),
                        TextEntry::make('verificationIdentity.replaced_at')
                            ->label('Remplacée le')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('—'),
                        TextEntry::make('verificationIdentity.replaced_by_document_number')
                            ->label('Remplacée par')
                            ->placeholder('—'),
                    ])->columns(2),
            ]);
    }
}
