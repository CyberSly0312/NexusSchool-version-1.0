<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources\SchoolClassResource\Pages;

use App\Domains\Academic\Presentation\Filament\Resources\SchoolClassResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSchoolClasses extends ListRecords
{
    protected static string $resource = SchoolClassResource::class;

    protected static ?string $title = 'Classes';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
