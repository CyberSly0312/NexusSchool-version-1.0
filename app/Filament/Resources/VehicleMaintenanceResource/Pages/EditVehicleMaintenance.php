<?php

declare(strict_types=1);

namespace App\Filament\Resources\VehicleMaintenanceResource\Pages;

use App\Filament\Resources\VehicleMaintenanceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVehicleMaintenance extends EditRecord
{
    protected static string $resource = VehicleMaintenanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
