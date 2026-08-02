<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources\CoefficientResource\Pages;

use App\Domains\Academic\Presentation\Filament\Resources\CoefficientResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCoefficient extends CreateRecord
{
    protected static string $resource = CoefficientResource::class;

    protected static ?string $title = 'Créer un coefficient';
}
