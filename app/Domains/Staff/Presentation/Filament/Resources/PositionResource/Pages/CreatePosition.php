<?php

declare(strict_types=1);

namespace App\Domains\Staff\Presentation\Filament\Resources\PositionResource\Pages;

use App\Domains\Staff\Presentation\Filament\Resources\PositionResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePosition extends CreateRecord
{
    protected static string $resource = PositionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
