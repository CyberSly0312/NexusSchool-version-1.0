<?php

declare(strict_types=1);

namespace App\Filament\Resources\SystemHealthResource\Pages;

use App\Filament\Resources\SystemHealthResource;
use Filament\Resources\Pages\ListRecords;

class ListSystemHealth extends ListRecords
{
    protected static string $resource = SystemHealthResource::class;
}
