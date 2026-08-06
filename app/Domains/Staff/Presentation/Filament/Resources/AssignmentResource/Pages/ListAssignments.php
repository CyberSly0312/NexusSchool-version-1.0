<?php

declare(strict_types=1);

namespace App\Domains\Staff\Presentation\Filament\Resources\AssignmentResource\Pages;

use App\Domains\Staff\Presentation\Filament\Resources\AssignmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAssignments extends ListRecords
{
    protected static string $resource = AssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
