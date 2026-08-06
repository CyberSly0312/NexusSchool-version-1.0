<?php

declare(strict_types=1);

namespace App\Domains\Staff\Presentation\Filament\Resources\PositionResource\Pages;

use App\Domains\Staff\Presentation\Filament\Resources\PositionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPositions extends ListRecords
{
    protected static string $resource = PositionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
