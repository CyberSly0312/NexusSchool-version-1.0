<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Domain\References;

use App\Domains\ClassCouncil\Domain\Enums\CouncilDecisionType;

/**
 * The automatic decision the engine derives from the published academic
 * standing (SPR-013). The council may override it with a recorded decision,
 * provided the override carries a justification and an approval.
 */
final readonly class AutomaticDecisionReference
{
    public function __construct(
        public CouncilDecisionType $type,
    ) {
    }

    public function label(): string
    {
        return $this->type->label();
    }
}
