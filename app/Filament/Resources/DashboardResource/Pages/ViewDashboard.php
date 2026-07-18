<?php

declare(strict_types=1);

namespace App\Filament\Resources\DashboardResource\Pages;

use App\Filament\Resources\DashboardResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDashboard extends ViewRecord
{
    protected static string $resource = DashboardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
