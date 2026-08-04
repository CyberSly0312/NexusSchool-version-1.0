<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Presentation\Filament\Resources\EnrollmentResource\Pages;

use App\Domains\Enrollment\Application\DTOs\CreateEnrollmentData;
use App\Domains\Enrollment\Application\Services\EnrollmentService;
use App\Domains\Enrollment\Presentation\Filament\Resources\EnrollmentResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateEnrollment extends CreateRecord
{
    protected static string $resource = EnrollmentResource::class;

    protected static ?string $title = 'Nouvelle inscription';

    protected function handleRecordCreation(array $data): Model
    {
        $data['school_id'] = tenant()?->id ?? auth()->user()?->school_id ?? '';
        $data['enrolled_at'] = $data['enrolled_at'] ?? now()->toDateString();

        return app(EnrollmentService::class)->enroll(
            CreateEnrollmentData::fromArray($data),
            auth()->user(),
        );
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
