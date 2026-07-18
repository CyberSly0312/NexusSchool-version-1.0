<?php

declare(strict_types=1);

namespace App\Domains\School\Exceptions;

use RuntimeException;

class SchoolNotFoundException extends RuntimeException
{
    public static function byId(string $id): static
    {
        return new static("School with ID [{$id}] not found.");
    }

    public static function bySlug(string $slug): static
    {
        return new static("School with slug [{$slug}] not found.");
    }

    public static function byDomain(string $domain): static
    {
        return new static("School with domain [{$domain}] not found.");
    }
}
