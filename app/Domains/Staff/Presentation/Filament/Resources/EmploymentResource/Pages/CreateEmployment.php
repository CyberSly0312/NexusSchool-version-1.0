<?php

declare(strict_types=1);

namespace App\Domains\Staff\Presentation\Filament\Resources\EmploymentResource\Pages;

use App\Domains\Staff\Application\Services\StaffService;
use App\Domains\Staff\Infrastructure\Models\Employment;
use App\Domains\Staff\Presentation\Filament\Resources\EmploymentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEmployment extends CreateRecord
{
    protected static string $resource = EmploymentResource::class;

    protected function handleRecordCreation(array $data): Employment
    {
        $schoolId = (string) tenant()->id;

        return app(StaffService::class)->startEmployment($schoolId, $data, auth()->user());
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
