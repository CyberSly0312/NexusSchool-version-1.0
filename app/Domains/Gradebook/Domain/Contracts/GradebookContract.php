<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Domain\Contracts;

use App\Domains\Gradebook\Domain\References\GradebookReference;

/**
 * Read-only gateway to gradebook aggregates (SPR-010). Implementations live in
 * the Infrastructure layer of this domain; the application actions depend on
 * this interface so they never see a Gradebook model directly.
 */
interface GradebookContract
{
    public function gradebook(string $schoolId, int|string $gradebookId): ?GradebookReference;
}
