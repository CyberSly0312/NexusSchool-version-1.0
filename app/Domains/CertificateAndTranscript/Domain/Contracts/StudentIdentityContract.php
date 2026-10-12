<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Domain\Contracts;

use App\Domains\CertificateAndTranscript\Domain\References\StudentReference;

/**
 * Read-only gateway to the Student identity domain (SPR-015).
 */
interface StudentIdentityContract
{
    public function student(string $schoolId, int|string $studentId): ?StudentReference;
}
