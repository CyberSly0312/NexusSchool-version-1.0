<?php

declare(strict_types=1);

namespace App\Filament\Resources\BookCategoryResource\Pages;

use App\Filament\Resources\BookCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBookCategories extends ListRecords
{
    protected static string $resource = BookCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
