<?php

declare(strict_types=1);

namespace App\Filament\Resources\DashboardResource\Pages;

use App\Filament\Resources\DashboardResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDashboard extends CreateRecord
{
    protected static string $resource = DashboardResource::class;
}
