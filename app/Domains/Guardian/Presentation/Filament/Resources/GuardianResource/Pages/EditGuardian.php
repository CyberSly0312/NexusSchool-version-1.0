<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Presentation\Filament\Resources\GuardianResource\Pages;

use App\Domains\Guardian\Application\Services\GuardianService;
use App\Domains\Guardian\Infrastructure\Models\Guardian;
use App\Domains\Guardian\Presentation\Filament\Resources\GuardianResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditGuardian extends EditRecord
{
    protected static string $resource = GuardianResource::class;

    protected static ?string $title = 'Modifier un tuteur';

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return app(GuardianService::class)->update($this->guardian(), $data, auth()->user());
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    private function guardian(): Guardian
    {
        return Guardian::query()->findOrFail($this->record->getKey());
    }
}
