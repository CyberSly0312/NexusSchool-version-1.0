<?php

declare(strict_types=1);

namespace App\Filament\Resources\AiConversationResource\Pages;

use App\Filament\Resources\AiConversationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAiConversation extends CreateRecord
{
    protected static string $resource = AiConversationResource::class;
}
