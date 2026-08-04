<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Presentation\Filament\Resources\GuardianResource\Pages;

use App\Domains\Guardian\Presentation\Filament\Resources\GuardianResource;
use Filament\Resources\Pages\ListRecords;

class ListGuardians extends ListRecords
{
    protected static string $resource = GuardianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
