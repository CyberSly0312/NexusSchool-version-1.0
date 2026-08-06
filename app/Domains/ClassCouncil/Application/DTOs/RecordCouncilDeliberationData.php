<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Application\DTOs;

/**
 * Input of the deliberation record action (SPR-013). Deliberation records are
 * confidential: they stay inside the Class Council engine and are never exposed
 * through a contract or rendered on a document.
 */
final readonly class RecordCouncilDeliberationData
{
    public function __construct(
        public string $schoolId,
        public int|string $sessionId,
        public string $summary,
        public string $topic = 'Général',
        public int|string|null $studentId = null,
        public int|string|null $decisionId = null,
        public ?string $member = null,
        public ?string $notedAt = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: (string) $data['school_id'],
            sessionId: (int) $data['council_session_id'],
            summary: (string) ($data['summary'] ?? ''),
            topic: (string) ($data['topic'] ?? 'Général'),
            studentId: isset($data['student_id']) ? (int) $data['student_id'] : null,
            decisionId: isset($data['council_decision_id']) ? (int) $data['council_decision_id'] : null,
            member: isset($data['member']) ? (string) $data['member'] : null,
            notedAt: isset($data['noted_at']) ? (string) $data['noted_at'] : null,
        );
    }
}
