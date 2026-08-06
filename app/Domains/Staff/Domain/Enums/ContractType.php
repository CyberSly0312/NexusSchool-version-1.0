<?php

declare(strict_types=1);

namespace App\Domains\Staff\Domain\Enums;

/**
 * Legal nature of the employment contract.
 */
enum ContractType: string
{
    case Permanent = 'permanent';
    case FixedTerm = 'fixed_term';
    case Internship = 'internship';
    case Temporary = 'temporary';
    case Contract = 'contract';

    public function label(): string
    {
        return match ($this) {
            self::Permanent => 'Contrat à durée indéterminée',
            self::FixedTerm => 'Contrat à durée déterminée',
            self::Internship => 'Stage',
            self::Temporary => 'Temporaire',
            self::Contract => 'Prestation / Contrat',
        };
    }
}
