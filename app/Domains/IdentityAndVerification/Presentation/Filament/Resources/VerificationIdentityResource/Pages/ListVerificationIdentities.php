<?php

declare(strict_types=1);

namespace App\Domains\IdentityAndVerification\Presentation\Filament\Resources\VerificationIdentityResource\Pages;

use App\Domains\IdentityAndVerification\Presentation\Filament\Resources\VerificationIdentityResource;
use Filament\Resources\Pages\ListRecords;

class ListVerificationIdentities extends ListRecords
{
    protected static string $resource = VerificationIdentityResource::class;

    protected static ?string $title = 'Vérifications de documents';
}
