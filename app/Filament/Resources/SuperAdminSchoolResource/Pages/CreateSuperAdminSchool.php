<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuperAdminSchoolResource\Pages;

use App\Domains\School\DTOs\SchoolData;
use App\Domains\School\Services\SchoolService;
use App\Filament\Resources\SuperAdminSchoolResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateSuperAdminSchool extends CreateRecord
{
    protected static string $resource = SuperAdminSchoolResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        unset($data['create_admin'], $data['admin_name'], $data['admin_email']);

        return app(SchoolService::class)->create(SchoolData::fromArray($data));
    }
}
