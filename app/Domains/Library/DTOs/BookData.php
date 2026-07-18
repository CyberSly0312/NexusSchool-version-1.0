<?php

declare(strict_types=1);

namespace App\Domains\Library\DTOs;

readonly class BookData
{
    public function __construct(
        public string $schoolId,
        public string $title,
        public ?string $isbn = null,
        public ?string $subtitle = null,
        public ?string $description = null,
        public string $language = 'English',
        public ?string $edition = null,
        public ?int $publicationYear = null,
        public ?int $publisherId = null,
        public ?int $bookCategoryId = null,
        public ?string $shelfLocation = null,
        public ?string $coverImage = null,
        public string $status = 'active',
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            schoolId: $data['school_id'],
            title: $data['title'],
            isbn: $data['isbn'] ?? null,
            subtitle: $data['subtitle'] ?? null,
            description: $data['description'] ?? null,
            language: $data['language'] ?? 'English',
            edition: $data['edition'] ?? null,
            publicationYear: $data['publication_year'] ?? null,
            publisherId: $data['publisher_id'] ?? null,
            bookCategoryId: $data['book_category_id'] ?? null,
            shelfLocation: $data['shelf_location'] ?? null,
            coverImage: $data['cover_image'] ?? null,
            status: $data['status'] ?? 'active',
        );
    }

    public function toArray(): array
    {
        return [
            'school_id' => $this->schoolId,
            'title' => $this->title,
            'isbn' => $this->isbn,
            'subtitle' => $this->subtitle,
            'description' => $this->description,
            'language' => $this->language,
            'edition' => $this->edition,
            'publication_year' => $this->publicationYear,
            'publisher_id' => $this->publisherId,
            'book_category_id' => $this->bookCategoryId,
            'shelf_location' => $this->shelfLocation,
            'cover_image' => $this->coverImage,
            'status' => $this->status,
        ];
    }
}
