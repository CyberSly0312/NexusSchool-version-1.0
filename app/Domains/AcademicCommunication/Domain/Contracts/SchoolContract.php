<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\Contracts;

use App\Domains\AcademicCommunication\Domain\References\SchoolReference;

/**
 * Read-only gateway to the School (tenant) record (SPR-012). Provides the
 * branding material (name, logo, address, registration number) embedded in
 * every generated document and the school code used by the document identity.
 */
interface SchoolContract
{
    public function school(string $schoolId): ?SchoolReference;
}
