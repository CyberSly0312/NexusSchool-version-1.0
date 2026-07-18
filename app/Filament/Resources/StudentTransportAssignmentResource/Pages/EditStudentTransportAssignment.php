<?php

declare(strict_types=1);

namespace App\Filament\Resources\StudentTransportAssignmentResource\Pages;

use App\Filament\Resources\StudentTransportAssignmentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStudentTransportAssignment extends EditRecord
{
    protected static string $resource = StudentTransportAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
