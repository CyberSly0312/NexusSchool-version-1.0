<?php

declare(strict_types=1);

namespace App\Filament\Resources\BookCategoryResource\Pages;

use App\Filament\Resources\BookCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBookCategory extends EditRecord
{
    protected static string $resource = BookCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
