<?php

declare(strict_types=1);

namespace App\Domains\Student\Presentation\Filament\Resources\StudentResource\Pages;

use App\Domains\Student\Application\DTOs\CreateStudentData;
use App\Domains\Student\Application\Services\StudentService;
use App\Domains\Student\Domain\ValueObjects\GuardianReference;
use App\Domains\Student\Presentation\Filament\Resources\StudentResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    protected static ?string $title = 'Créer un élève';

    protected function handleRecordCreation(array $data): Model
    {
        $guardians = $this->buildGuardians($data['guardians'] ?? []);
        unset($data['guardians']);

        $data['school_id'] = tenant()?->id ?? auth()->user()?->school_id ?? '';

        $dto = CreateStudentData::fromArray($data)->withGuardians($guardians);

        return app(StudentService::class)->register($dto, auth()->user());
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    /**
     * @param  list<array<string, mixed>>  $rawGuardians
     * @return list<GuardianReference>
     */
    private function buildGuardians(array $rawGuardians): array
    {
        return array_values(array_filter(array_map(
            fn (array $g): ?GuardianReference => GuardianReference::fromNullableGuardian($g),
            $rawGuardians,
        )));
    }
}
