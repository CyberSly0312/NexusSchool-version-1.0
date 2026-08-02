<?php

declare(strict_types=1);

namespace App\Domains\Academic\Domain\Enums;

/**
 * ISO 3166-1 alpha-2 codes of the countries the platform targets (WAEMU/UEMOA zone).
 *
 * The engine is country-ready: every catalogue entity may later carry a country
 * discriminator without schema change (countries are reference data seeded per school).
 */
enum CountryCode: string
{
    case BurkinaFaso = 'BF';
    case CoteDIvoire = 'CI';
    case Mali = 'ML';
    case Niger = 'NE';
    case Togo = 'TG';
    case Benin = 'BJ';
    case Senegal = 'SN';

    public function label(): string
    {
        return match ($this) {
            self::BurkinaFaso => 'Burkina Faso',
            self::CoteDIvoire => "Côte d'Ivoire",
            self::Mali => 'Mali',
            self::Niger => 'Niger',
            self::Togo => 'Togo',
            self::Benin => 'Bénin',
            self::Senegal => 'Sénégal',
        };
    }
}
