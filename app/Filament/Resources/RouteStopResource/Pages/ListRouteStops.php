<?php

declare(strict_types=1);

namespace App\Filament\Resources\RouteStopResource\Pages;

use App\Filament\Resources\RouteStopResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRouteStops extends ListRecords
{
    protected static string $resource = RouteStopResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
