<?php

declare(strict_types=1);

namespace App\Filament\Resources\VehicleMaintenanceResource\Pages;

use App\Filament\Resources\VehicleMaintenanceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVehicleMaintenances extends ListRecords
{
    protected static string $resource = VehicleMaintenanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
