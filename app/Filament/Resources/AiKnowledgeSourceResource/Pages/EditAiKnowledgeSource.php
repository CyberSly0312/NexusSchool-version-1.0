<?php

declare(strict_types=1);

namespace App\Filament\Resources\AiKnowledgeSourceResource\Pages;

use App\Filament\Resources\AiKnowledgeSourceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAiKnowledgeSource extends EditRecord
{
    protected static string $resource = AiKnowledgeSourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
