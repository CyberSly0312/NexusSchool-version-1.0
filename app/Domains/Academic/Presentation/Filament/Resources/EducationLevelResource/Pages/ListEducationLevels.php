<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources\EducationLevelResource\Pages;

use App\Domains\Academic\Presentation\Filament\Resources\EducationLevelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEducationLevels extends ListRecords
{
    protected static string $resource = EducationLevelResource::class;

    protected static ?string $title = 'Niveaux';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
