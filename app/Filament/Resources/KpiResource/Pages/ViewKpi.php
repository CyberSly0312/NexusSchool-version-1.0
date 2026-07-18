<?php

declare(strict_types=1);

namespace App\Filament\Resources\KpiResource\Pages;

use App\Filament\Resources\KpiResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewKpi extends ViewRecord
{
    protected static string $resource = KpiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
