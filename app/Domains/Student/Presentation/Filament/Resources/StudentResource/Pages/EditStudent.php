<?php

declare(strict_types=1);

namespace App\Domains\Student\Presentation\Filament\Resources\StudentResource\Pages;

use App\Domains\Student\Application\DTOs\UpdateStudentData;
use App\Domains\Student\Application\Services\StudentService;
use App\Domains\Student\Domain\ValueObjects\GuardianReference;
use App\Domains\Student\Infrastructure\Models\Student;
use App\Domains\Student\Presentation\Filament\Resources\StudentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditStudent extends EditRecord
{
    protected static string $resource = StudentResource::class;

    protected static ?string $title = 'Modifier un élève';

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $guardians = $this->buildGuardians($data['guardians'] ?? []);
        unset($data['guardians']);

        $dto = UpdateStudentData::fromArray($data)->withGuardians($guardians);

        return app(StudentService::class)->update($this->student(), $dto, auth()->user());
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['guardians'] = $this->student()->guardians
            ->map(fn ($guardian): array => [
                'name' => $guardian->name,
                'phone' => $guardian->phone,
                'email' => $guardian->email,
                'relationship' => $guardian->relationship,
                'is_primary' => (bool) $guardian->is_primary,
            ])
            ->values()
            ->all();

        return $data;
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

    private function student(): Student
    {
        return Student::query()->findOrFail($this->record->getKey());
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
