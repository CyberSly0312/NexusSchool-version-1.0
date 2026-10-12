<?php

declare(strict_types=1);

namespace App\Domains\CertificateAndTranscript\Infrastructure\Contracts;

use App\Domains\CertificateAndTranscript\Domain\Contracts\EnrollmentContract;
use App\Domains\CertificateAndTranscript\Domain\References\EnrollmentReference;
use App\Domains\CertificateAndTranscript\Domain\References\GraduationReference;
use App\Domains\Enrollment\Domain\Enums\EnrollmentStatus;
use App\Domains\Enrollment\Domain\Enums\GraduationDecision;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use App\Domains\Enrollment\Infrastructure\Models\EnrollmentGraduation;

/**
 * EnrollmentContract implementation (SPR-015, per ADR-021 Decision 5).
 *
 * Provides composition-grade enrollment facts and graduation closure over the
 * student's retained history. Read-only by construction: the adapter queries
 * with school scoping only and never exposes a mutation surface.
 */
final class EnrollmentAdapter implements EnrollmentContract
{
    public function enrollmentHistory(string $schoolId, int|string $studentId): array
    {
        return Enrollment::query()
            ->forSchool($schoolId)
            ->where('student_id', $studentId)
            ->with(['educationLevel', 'schoolClass', 'section'])
            ->orderBy('academic_calendar_year_id')
            ->orderBy('id')
            ->get()
            ->map(function (Enrollment $enrollment): EnrollmentReference {
                $class = $enrollment->schoolClass;
                $section = $enrollment->section;
                $level = $enrollment->educationLevel;
                $status = $enrollment->getAttribute('status');
                $status = $status instanceof EnrollmentStatus ? $status->value : ($status === null ? null : (string) $status);

                return new EnrollmentReference(
                    id: $enrollment->getKey(),
                    studentId: $enrollment->student_id,
                    yearId: $enrollment->academic_calendar_year_id,
                    classId: $enrollment->school_class_id,
                    className: $class?->getAttribute('name'),
                    sectionId: $enrollment->class_section_id,
                    sectionName: $section?->getAttribute('name'),
                    educationLevelId: $level?->getKey(),
                    educationLevel: $level?->getAttribute('name'),
                    status: $status,
                    enrollmentNumber: $enrollment->enrollment_number,
                );
            })
            ->all();
    }

    public function graduationRecords(string $schoolId, int|string $studentId): array
    {
        return EnrollmentGraduation::query()
            ->forSchool($schoolId)
            ->where('student_id', $studentId)
            ->with('educationLevel')
            ->orderBy('id')
            ->get()
            ->map(function (EnrollmentGraduation $graduation): GraduationReference {
                $level = $graduation->educationLevel;
                $graduatedOn = $graduation->getAttribute('graduated_on');
                if ($graduatedOn instanceof \DateTimeInterface) {
                    $graduatedOn = $graduatedOn->format('Y-m-d');
                }
                $graduatedOn = $graduatedOn === null ? null : (string) $graduatedOn;

                $decision = $graduation->getAttribute('decision');
                $decision = $decision instanceof GraduationDecision ? $decision->value : ($decision === null ? null : (string) $decision);

                return new GraduationReference(
                    id: $graduation->getKey(),
                    enrollmentId: $graduation->enrollment_id,
                    studentId: $graduation->student_id,
                    decision: $decision,
                    graduatedOn: $graduatedOn,
                    certificateReference: $graduation->certificate_reference,
                    educationLevelId: $level?->getKey(),
                    educationLevel: $level?->getAttribute('name'),
                );
            })
            ->all();
    }
}
