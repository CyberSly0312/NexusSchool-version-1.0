<?php

declare(strict_types=1);

namespace App\Domains\Staff\Presentation\Filament\Resources\PositionResource\Pages;

use App\Domains\Staff\Presentation\Filament\Resources\PositionResource;
use Filament\Resources\Pages\EditRecord;

class EditPosition extends EditRecord
{
    protected static string $resource = PositionResource::class;

    protected static ?string $title = 'Modifier un poste';

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
