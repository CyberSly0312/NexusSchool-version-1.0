<?php

declare(strict_types=1);

namespace App\Filament\Resources\AiConversationResource\Pages;

use App\Filament\Resources\AiConversationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAiConversation extends EditRecord
{
    protected static string $resource = AiConversationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
