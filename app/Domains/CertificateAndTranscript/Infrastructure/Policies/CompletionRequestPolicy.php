<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Infrastructure\Policies;

use App\Domains\CertificateAndTranscript\Domain\Traits\HandlesCertificateAndTranscriptAuthorization;

final class CompletionRequestPolicy
{
    use HandlesCertificateAndTranscriptAuthorization;

    protected function permissionGroup(): string
    {
        return 'certificate-and-transcript';
    }
}
