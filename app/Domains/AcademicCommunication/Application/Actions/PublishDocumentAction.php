<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Application\Actions;

use App\Domains\AcademicCommunication\Application\DTOs\PublicationPayload;
use App\Domains\AcademicCommunication\Application\DTOs\PublishDocumentData;
use App\Domains\AcademicCommunication\Application\Services\PublicationDispatcher;
use App\Domains\AcademicCommunication\Application\Services\PublicationRecipientResolver;
use App\Domains\AcademicCommunication\Domain\Contracts\DocumentRendererContract;
use App\Domains\AcademicCommunication\Domain\Contracts\PublicationChannelContract;
use App\Domains\AcademicCommunication\Domain\Enums\ApprovalStatus;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentChannel;
use App\Domains\AcademicCommunication\Domain\Enums\DocumentStatus;
use App\Domains\AcademicCommunication\Domain\Enums\PublicationStatus;
use App\Domains\AcademicCommunication\Domain\Events\DocumentPublished;
use App\Domains\AcademicCommunication\Domain\Events\PublicationCompleted;
use App\Domains\AcademicCommunication\Domain\Exceptions\DocumentNotApprovedException;
use App\Domains\AcademicCommunication\Domain\Exceptions\DocumentPublicationFailedException;
use App\Domains\AcademicCommunication\Domain\Exceptions\PublicationChannelUnavailableException;
use App\Domains\AcademicCommunication\Infrastructure\Models\AcademicDocument;
use App\Domains\AcademicCommunication\Infrastructure\Models\PublicationChannel;
use App\Domains\AcademicCommunication\Infrastructure\Models\PublicationDeliveryAttempt;
use App\Domains\AcademicCommunication\Infrastructure\Models\PublicationRecord;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Publishes a document through the channels enabled for its school (SPR-012).
 *
 * A document whose policy requires approval must be Approved before this action
 * runs. One PublicationRecord is created per (channel, recipient); deliveries
 * are attempted synchronously and each attempt is recorded. The document
 * transitions to Published as soon as at least one delivery succeeds — failed
 * records stay retryable and are never overwritten. Published versions become
 * immutable (RegenerateDocumentAction creates a new version instead).
 */
final class PublishDocumentAction
{
    public function __construct(
        private readonly PublicationDispatcher $dispatcher,
        private readonly PublicationRecipientResolver $recipientResolver,
        private readonly DocumentRendererContract $renderer,
    ) {
    }

    public function execute(PublishDocumentData $data, ?User $actor = null): AcademicDocument
    {
        return DB::transaction(function () use ($data, $actor): AcademicDocument {
            $document = AcademicDocument::query()
                ->forSchool($data->schoolId)
                ->findOrFail($data->documentId);

            $document->assertCanTransitionTo(DocumentStatus::Published);

            if ($document->requiresApproval() && $document->approval_status !== ApprovalStatus::Approved) {
                throw DocumentNotApprovedException::forDocument($document->getKey());
            }

            $version = $document->currentVersion;
            $template = $document->template;
            $policy = $document->publicationPolicy;

            if ($version === null || $template === null || $policy === null) {
                throw DocumentPublicationFailedException::noDelivery($document->document_number);
            }

            $channels = $this->requestedChannels($data, $policy->channels ?? []);

            $registry = PublicationChannel::query()
                ->forSchool($data->schoolId)
                ->get()
                ->keyBy(fn (PublicationChannel $channel): string => $channel->key->value);

            $recipients = $this->recipientResolver->resolve($document, $policy, $data->schoolId);

            if ($recipients === []) {
                throw DocumentPublicationFailedException::noDelivery($document->document_number);
            }

            $completed = 0;

            foreach ($channels as $channelValue) {
                $channel = $this->requireChannel($channelValue, $registry, $data->schoolId);
                $driver = $this->dispatcher->requireDriver($channel, $data->schoolId);

                foreach ($recipients as $recipient) {
                    $html = $this->renderer->renderHtml(
                        $version,
                        $template,
                        $policy->visibilityPolicy(),
                        $recipient->visibilityScope,
                    );

                    $record = PublicationRecord::create([
                        'school_id' => $data->schoolId,
                        'academic_document_id' => $document->getKey(),
                        'document_version_id' => $version->getKey(),
                        'channel' => $channel,
                        'recipient_type' => $recipient->recipientType,
                        'recipient_id' => $recipient->recipientId,
                        'recipient_label' => $recipient->recipientLabel,
                        'visibility_scope' => $recipient->visibilityScope,
                        'status' => PublicationStatus::Queued,
                        'attempts' => 0,
                    ]);

                    $this->deliver($record, $driver, new PublicationPayload(
                        schoolId: $data->schoolId,
                        documentId: $document->getKey(),
                        versionId: $version->getKey(),
                        templateId: $template->getKey(),
                        channel: $channel,
                        recipientType: $recipient->recipientType,
                        recipientId: $recipient->recipientId,
                        recipientLabel: $recipient->recipientLabel,
                        visibilityScope: $recipient->visibilityScope,
                        html: $html,
                        metadata: $recipient->metadata,
                    ));

                    if ($record->isCompleted()) {
                        $completed++;
                    }
                }
            }

            if ($completed === 0) {
                throw DocumentPublicationFailedException::noDelivery($document->document_number);
            }

            $document->update([
                'status' => DocumentStatus::Published,
                'published_at' => now(),
            ]);

            $version->update([
                'status' => DocumentStatus::Published,
                'is_published' => true,
                'published_at' => now(),
            ]);

            $document->refresh();

            DocumentPublished::dispatch($document, $version->refresh(), $actor);

            return $document;
        });
    }

    /**
     * @param  list<string>  $policyChannels
     *
     * @return list<string>
     */
    private function requestedChannels(PublishDocumentData $data, array $policyChannels): array
    {
        $candidates = $data->channels !== [] ? $data->channels : $policyChannels;

        return array_values(array_unique(array_map('strval', $candidates)));
    }

    /**
     * @param  \Illuminate\Support\Collection<string, PublicationChannel>  $registry
     */
    private function requireChannel(
        string $channelValue,
        \Illuminate\Support\Collection $registry,
        string $schoolId,
    ): DocumentChannel {
        $channel = DocumentChannel::tryFrom($channelValue);

        if ($channel === null) {
            throw PublicationChannelUnavailableException::forChannel($channelValue, $schoolId);
        }

        $enabled = $registry->get($channel->value);

        if ($enabled === null || ! $enabled->isEnabled()) {
            throw PublicationChannelUnavailableException::forChannel($channel->value, $schoolId);
        }

        return $channel;
    }

    private function deliver(
        PublicationRecord $record,
        PublicationChannelContract $driver,
        PublicationPayload $payload,
    ): void {
        $record->update([
            'status' => PublicationStatus::InProgress,
            'attempts' => ($record->attempts ?? 0) + 1,
        ]);

        $attempt = PublicationDeliveryAttempt::create([
            'school_id' => $record->school_id,
            'publication_record_id' => $record->getKey(),
            'status' => PublicationStatus::InProgress,
            'attempted_at' => now(),
        ]);

        try {
            $outcome = $driver->deliver($payload);

            if ($outcome->success) {
                $record->update([
                    'status' => PublicationStatus::Completed,
                    'published_at' => now(),
                    'last_error' => null,
                ]);

                $attempt->update([
                    'status' => PublicationStatus::Completed,
                    'completed_at' => now(),
                ]);

                PublicationCompleted::dispatch($record->document, $record->refresh(), $payload->metadata['actor'] ?? null);
            } else {
                $this->markFailed($record, $attempt, (string) ($outcome->error ?? 'Delivery failed.'));
            }
        } catch (\Throwable $exception) {
            $this->markFailed($record, $attempt, $exception->getMessage());
        }
    }

    private function markFailed(PublicationRecord $record, PublicationDeliveryAttempt $attempt, string $error): void
    {
        $record->update([
            'status' => PublicationStatus::Failed,
            'last_error' => $error,
        ]);

        $attempt->update([
            'status' => PublicationStatus::Failed,
            'error' => $error,
            'completed_at' => now(),
        ]);
    }
}
