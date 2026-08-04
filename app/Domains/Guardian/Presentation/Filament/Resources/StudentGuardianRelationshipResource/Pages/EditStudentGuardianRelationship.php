<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Presentation\Filament\Resources\StudentGuardianRelationshipResource\Pages;

use App\Domains\Guardian\Presentation\Filament\Resources\StudentGuardianRelationshipResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditStudentGuardianRelationship extends EditRecord
{
    protected static string $resource = StudentGuardianRelationshipResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
