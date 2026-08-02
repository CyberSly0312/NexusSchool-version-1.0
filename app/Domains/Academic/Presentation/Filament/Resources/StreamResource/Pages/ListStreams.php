<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources\StreamResource\Pages;

use App\Domains\Academic\Presentation\Filament\Resources\StreamResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStreams extends ListRecords
{
    protected static string $resource = StreamResource::class;

    protected static ?string $title = 'Filières';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
