<?php

declare(strict_types=1);

namespace App\Domains\Library\DTOs;

readonly class BookCopyData
{
    public function __construct(
        public string $schoolId,
        public int $bookId,
        public string $barcode,
        public string $inventoryNumber,
        public ?string $acquisitionDate = null,
        public ?float $purchaseCost = null,
        public string $condition = 'good',
        public string $status = 'available',
        public ?string $notes = null,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: $data['school_id'],
            bookId: $data['book_id'],
            barcode: $data['barcode'],
            inventoryNumber: $data['inventory_number'],
            acquisitionDate: $data['acquisition_date'] ?? null,
            purchaseCost: $data['purchase_cost'] ?? null,
            condition: $data['condition'] ?? 'good',
            status: $data['status'] ?? 'available',
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'school_id' => $this->schoolId,
            'book_id' => $this->bookId,
            'barcode' => $this->barcode,
            'inventory_number' => $this->inventoryNumber,
            'acquisition_date' => $this->acquisitionDate,
            'purchase_cost' => $this->purchaseCost,
            'condition' => $this->condition,
            'is_available' => $this->status === 'available',
            'status' => $this->status,
            'notes' => $this->notes,
        ];
    }
}
