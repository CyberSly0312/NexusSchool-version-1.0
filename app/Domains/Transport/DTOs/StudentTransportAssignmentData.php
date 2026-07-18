<?php

declare(strict_types=1);

namespace App\Domains\Transport\DTOs;

use App\Domains\Transport\Enums\AssignmentStatus;

readonly class StudentTransportAssignmentData
{
    public function __construct(
        public ?string $id,
        public string $school_id,
        public string $student_id,
        public string $route_id,
        public ?string $pickup_stop_id,
        public ?string $dropoff_stop_id,
        public string $academic_year_id,
        public ?string $academic_term_id,
        public ?float $monthly_fee,
        public AssignmentStatus $status,
        public ?string $enrolled_at,
        public ?string $notes,
    ) {
    }

    public static function create(mixed $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            school_id: $data['school_id'],
            student_id: $data['student_id'],
            route_id: $data['route_id'],
            pickup_stop_id: $data['pickup_stop_id'] ?? null,
            dropoff_stop_id: $data['dropoff_stop_id'] ?? null,
            academic_year_id: $data['academic_year_id'],
            academic_term_id: $data['academic_term_id'] ?? null,
            monthly_fee: $data['monthly_fee'] ?? null,
            status: AssignmentStatus::from($data['status'] ?? 'active'),
            enrolled_at: $data['enrolled_at'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'school_id' => $this->school_id,
            'student_id' => $this->student_id,
            'route_id' => $this->route_id,
            'pickup_stop_id' => $this->pickup_stop_id,
            'dropoff_stop_id' => $this->dropoff_stop_id,
            'academic_year_id' => $this->academic_year_id,
            'academic_term_id' => $this->academic_term_id,
            'monthly_fee' => $this->monthly_fee,
            'status' => $this->status->value,
            'enrolled_at' => $this->enrolled_at,
            'notes' => $this->notes,
        ];
    }
}
