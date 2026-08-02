<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources\ClassSectionResource\Pages;

use App\Domains\Academic\Presentation\Filament\Resources\ClassSectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListClassSections extends ListRecords
{
    protected static string $resource = ClassSectionResource::class;

    protected static ?string $title = 'Sections';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
