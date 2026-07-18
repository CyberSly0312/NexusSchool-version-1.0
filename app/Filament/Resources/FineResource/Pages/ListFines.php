<?php

declare(strict_types=1);

namespace App\Filament\Resources\FineResource\Pages;

use App\Filament\Resources\FineResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFines extends ListRecords
{
    protected static string $resource = FineResource::class;

    protected static ?string $title = 'Fines';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
