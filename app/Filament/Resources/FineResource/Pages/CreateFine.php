<?php

declare(strict_types=1);

namespace App\Filament\Resources\FineResource\Pages;

use App\Filament\Resources\FineResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFine extends CreateRecord
{
    protected static string $resource = FineResource::class;
}
