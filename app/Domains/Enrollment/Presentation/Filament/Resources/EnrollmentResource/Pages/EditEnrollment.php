<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Presentation\Filament\Resources\EnrollmentResource\Pages;

use App\Domains\Enrollment\Application\DTOs\UpdateEnrollmentData;
use App\Domains\Enrollment\Application\Services\EnrollmentService;
use App\Domains\Enrollment\Infrastructure\Models\Enrollment;
use App\Domains\Enrollment\Presentation\Filament\Resources\EnrollmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditEnrollment extends EditRecord
{
    protected static string $resource = EnrollmentResource::class;

    protected static ?string $title = 'Modifier une inscription';

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $dto = UpdateEnrollmentData::fromArray($data);

        return app(EnrollmentService::class)->update($this->enrollment(), $dto, auth()->user());
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('Supprimer'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    private function enrollment(): Enrollment
    {
        return Enrollment::query()->findOrFail($this->record->getKey());
    }
}
