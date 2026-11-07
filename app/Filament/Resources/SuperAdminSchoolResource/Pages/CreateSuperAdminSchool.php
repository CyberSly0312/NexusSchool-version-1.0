<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuperAdminSchoolResource\Pages;

use App\Domains\School\DTOs\SchoolData;
use App\Domains\School\Services\SchoolProvisioningService;
use App\Filament\Resources\SuperAdminSchoolResource;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateSuperAdminSchool extends CreateRecord
{
    protected static string $resource = SuperAdminSchoolResource::class;

    protected ?string $schoolAdminSetupUrl = null;

    protected function handleRecordCreation(array $data): Model
    {
        $adminName = (string) ($data['admin_name'] ?? '');
        $adminEmail = (string) ($data['admin_email'] ?? '');

        unset($data['admin_name'], $data['admin_email']);

        $actor = auth()->user();

        $result = app(SchoolProvisioningService::class)->create(
            SchoolData::fromArray($data),
            $adminName,
            $adminEmail,
            $actor instanceof User ? $actor : null,
        );

        $this->schoolAdminSetupUrl = $result->administrator->setupUrl;

        return $result->school;
    }

    protected function afterCreate(): void
    {
        if ($this->schoolAdminSetupUrl === null) {
            return;
        }

        Notification::make()
            ->title('École et administrateur créés')
            ->body('Lien de configuration à usage unique (24 h) : '.$this->schoolAdminSetupUrl)
            ->success()
            ->persistent()
            ->send();
    }
}
