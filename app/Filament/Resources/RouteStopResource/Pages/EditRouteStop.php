<?php

declare(strict_types=1);

namespace App\Filament\Resources\RouteStopResource\Pages;

use App\Filament\Resources\RouteStopResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRouteStop extends EditRecord
{
    protected static string $resource = RouteStopResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
