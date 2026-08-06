<?php

declare(strict_types=1);

namespace App\Domains\Staff\Presentation\Filament\Resources\StaffResource\Pages;

use App\Domains\Staff\Application\Services\StaffService;
use App\Domains\Staff\Infrastructure\Models\Staff;
use App\Domains\Staff\Presentation\Filament\Resources\StaffResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStaff extends CreateRecord
{
    protected static string $resource = StaffResource::class;

    protected function handleRecordCreation(array $data): Staff
    {
        $schoolId = (string) tenant()->id;

        return app(StaffService::class)->create($schoolId, $data, auth()->user());
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
