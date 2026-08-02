<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources\StreamResource\Pages;

use App\Domains\Academic\Presentation\Filament\Resources\StreamResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStream extends EditRecord
{
    protected static string $resource = StreamResource::class;

    protected static ?string $title = 'Modifier une filière';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
