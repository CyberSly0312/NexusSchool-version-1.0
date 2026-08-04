<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Presentation\Filament\Resources\GuardianResource\Pages;

use App\Domains\Guardian\Application\Services\GuardianService;
use App\Domains\Guardian\Infrastructure\Models\Guardian;
use App\Domains\Guardian\Presentation\Filament\Resources\GuardianResource;
use Filament\Resources\Pages\CreateRecord;

class CreateGuardian extends CreateRecord
{
    protected static string $resource = GuardianResource::class;

    protected function handleRecordCreation(array $data): Guardian
    {
        $schoolId = (int) tenant()->id;

        return app(GuardianService::class)->create($schoolId, $data, auth()->user());
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
