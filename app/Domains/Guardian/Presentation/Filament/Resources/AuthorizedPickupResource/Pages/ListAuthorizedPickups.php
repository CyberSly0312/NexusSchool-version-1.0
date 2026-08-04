<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Presentation\Filament\Resources\AuthorizedPickupResource\Pages;

use App\Domains\Guardian\Presentation\Filament\Resources\AuthorizedPickupResource;
use Filament\Resources\Pages\ListRecords;

class ListAuthorizedPickups extends ListRecords
{
    protected static string $resource = AuthorizedPickupResource::class;
}
