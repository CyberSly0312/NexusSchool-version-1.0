<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Domain\Events;

use App\Domains\CertificateAndTranscript\Infrastructure\Models\CompletionRequest;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Dispatched when the certificate eligibility orchestrator produces its verdict
 * and evidence citations for an end-of-study certificate (SPR-015).
 */
class CertificateEligibilityDetermined
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @param  array<string, mixed>  $verdict
     * @param  list<string>  $evidenceCitations
     */
    public function __construct(
        public readonly CompletionRequest $completionRequest,
        public readonly array $verdict,
        public readonly array $evidenceCitations = [],
        public readonly ?User $actor = null,
    ) {
    }
}
