<?php

declare(strict_types=1);

namespace App\Domains\Student\Domain\ValueObjects;

/**
 * Value object describing a student photo. Only stores the reference/URL; the
 * binary file itself is managed by the storage layer.
 */
final readonly class Photo
{
    private function __construct(public string $path)
    {
    }

    public static function from(string $path): self
    {
        $path = trim($path);

        if ($path === '') {
            throw new \InvalidArgumentException('Photo path must not be empty.');
        }

        return new self($path);
    }

    public static function empty(): self
    {
        return new self('');
    }

    public function isSet(): bool
    {
        return $this->path !== '';
    }

    public function value(): string
    {
        return $this->path;
    }

    public function __toString(): string
    {
        return $this->path;
    }
}
