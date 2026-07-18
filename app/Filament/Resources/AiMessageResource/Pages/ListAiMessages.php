<?php

declare(strict_types=1);

namespace App\Filament\Resources\AiMessageResource\Pages;

use App\Filament\Resources\AiMessageResource;
use Filament\Resources\Pages\ListRecords;

class ListAiMessages extends ListRecords
{
    protected static string $resource = AiMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
