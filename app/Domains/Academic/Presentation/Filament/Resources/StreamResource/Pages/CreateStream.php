<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources\StreamResource\Pages;

use App\Domains\Academic\Presentation\Filament\Resources\StreamResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStream extends CreateRecord
{
    protected static string $resource = StreamResource::class;

    protected static ?string $title = 'Créer une filière';
}
