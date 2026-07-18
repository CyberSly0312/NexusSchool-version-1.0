<?php

declare(strict_types=1);

namespace App\Filament\Resources\StudentTransportAssignmentResource\Pages;

use App\Filament\Resources\StudentTransportAssignmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStudentTransportAssignments extends ListRecords
{
    protected static string $resource = StudentTransportAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
