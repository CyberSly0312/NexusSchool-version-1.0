<?php

declare(strict_types=1);

namespace App\Domains\Attendance\Application\DTOs;

use App\Domains\Attendance\Domain\Enums\AttendanceStatus;
use App\Domains\Attendance\Domain\ValueObjects\CorrectionReason;

/**
 * Data transfer object for an authorized attendance correction (SPR-008).
 */
final readonly class CorrectionData
{
    public function __construct(
        public int|string $recordId,
        public AttendanceStatus $newStatus,
        public CorrectionReason $reason,
        public int|string|null $authorizedBy = null,
        public ?string $notes = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            recordId: (int) $data['attendance_record_id'],
            newStatus: AttendanceStatus::from((string) $data['new_status']),
            reason: CorrectionReason::forType(
                (string) ($data['reason_type'] ?? CorrectionReason::TYPE_OTHER),
                (string) ($data['reason_text'] ?? ''),
            ),
            authorizedBy: isset($data['authorized_by']) && $data['authorized_by'] !== ''
                ? (int) $data['authorized_by']
                : null,
            notes: isset($data['notes']) && trim((string) $data['notes']) !== ''
                ? trim((string) $data['notes'])
                : null,
        );
    }
}
