<?php

declare(strict_types=1);

namespace App\Filament\Resources\AiConversationResource\Pages;

use App\Filament\Resources\AiConversationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAiConversations extends ListRecords
{
    protected static string $resource = AiConversationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
