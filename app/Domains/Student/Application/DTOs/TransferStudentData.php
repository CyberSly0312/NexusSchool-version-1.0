<?php

declare(strict_types=1);

namespace App\Domains\Student\Application\DTOs;

use App\Domains\Student\Domain\Enums\TransferStatus;

final readonly class TransferStudentData
{
    public function __construct(
        public TransferStatus $transferStatus,
        public ?string $transferFromSchool = null,
        public ?string $transferToSchool = null,
        public ?string $transferNote = null,
        public ?int $schoolClassId = null,
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            transferStatus: TransferStatus::from($data['transfer_status']),
            transferFromSchool: $data['transfer_from_school'] ?? null,
            transferToSchool: $data['transfer_to_school'] ?? null,
            transferNote: $data['transfer_note'] ?? null,
            schoolClassId: isset($data['school_class_id']) ? (int) $data['school_class_id'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'transfer_status' => $this->transferStatus->value,
            'transfer_from_school' => $this->transferFromSchool,
            'transfer_to_school' => $this->transferToSchool,
            'transfer_note' => $this->transferNote,
            'school_class_id' => $this->schoolClassId,
        ];
    }
}
