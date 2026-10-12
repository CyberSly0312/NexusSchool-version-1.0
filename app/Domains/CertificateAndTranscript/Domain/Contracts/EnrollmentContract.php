<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Domain\Contracts;

use App\Domains\CertificateAndTranscript\Domain\References\EnrollmentReference;
use App\Domains\CertificateAndTranscript\Domain\References\GraduationReference;

/**
 * Read-only gateway to the Enrollment engine (SPR-015, per ADR-021 Decision 5).
 *
 * Provides composition-grade enrollment facts (academic year / term / class /
 * section, education level) and graduation closure (verdict, date, certificate
 * reference) over a student's retained history — without any mutation surface
 * and without leaking PII beyond the identity contract.
 */
interface EnrollmentContract
{
    /**
     * Enrollment periods of one student's retained history.
     *
     * @return list<EnrollmentReference>
     */
    public function enrollmentHistory(string $schoolId, int|string $studentId): array;

    /**
     * Graduation records closing one student's enrollments.
     *
     * @return list<GraduationReference>
     */
    public function graduationRecords(string $schoolId, int|string $studentId): array;
}
