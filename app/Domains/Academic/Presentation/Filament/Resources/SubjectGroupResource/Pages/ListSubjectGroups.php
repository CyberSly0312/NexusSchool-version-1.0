<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources\SubjectGroupResource\Pages;

use App\Domains\Academic\Presentation\Filament\Resources\SubjectGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSubjectGroups extends ListRecords
{
    protected static string $resource = SubjectGroupResource::class;

    protected static ?string $title = 'Groupes de matières';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
