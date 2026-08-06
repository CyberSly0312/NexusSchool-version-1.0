<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Domain\Contracts;

use App\Domains\AcademicCommunication\Domain\References\CouncilDecisionReference;

/**
 * Read-only gateway to the Class Council domain (SPR-012). The engine consumes
 * only *published* council decisions — confidential deliberations are never
 * exposed by this contract and can never reach a rendered document.
 */
interface ClassCouncilContract
{
    /**
     * Published decisions of one council session.
     *
     * @return list<CouncilDecisionReference>
     */
    public function publishedDecisions(
        string $schoolId,
        int|string $sessionId,
    ): array;
}
