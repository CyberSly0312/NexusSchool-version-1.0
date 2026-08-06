<?php

declare(strict_types=1);

namespace App\Domains\Staff\Presentation\Filament\Resources\AssignmentResource\Pages;

use App\Domains\Staff\Application\Services\StaffService;
use App\Domains\Staff\Domain\Enums\AssignmentType;
use App\Domains\Staff\Infrastructure\Models\Assignment;
use App\Domains\Staff\Presentation\Filament\Resources\AssignmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAssignment extends CreateRecord
{
    protected static string $resource = AssignmentResource::class;

    protected function handleRecordCreation(array $data): Assignment
    {
        $schoolId = (string) tenant()->id;
        $service = app(StaffService::class);
        $actor = auth()->user();

        return match ($data['assignment_type'] ?? null) {
            AssignmentType::Department->value => $service->assignDepartment($schoolId, $data, $actor),
            AssignmentType::Position->value => $service->assignPosition($schoolId, $data, $actor),
            default => Assignment::create($data),
        };
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
