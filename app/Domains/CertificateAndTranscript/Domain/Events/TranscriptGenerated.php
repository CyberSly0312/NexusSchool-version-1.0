<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Domain\Events;

use App\Domains\CertificateAndTranscript\Infrastructure\Models\CompletionRequest;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TranscriptGenerated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly CompletionRequest $completionRequest,
        public readonly ?string $documentNumber = null,
        public readonly ?User $actor = null,
    ) {
    }
}
