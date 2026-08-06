<?php

declare(strict_types=1);

namespace App\Domains\Staff\Presentation\Filament\Resources\StaffResource\Pages;

use App\Domains\Staff\Application\Services\StaffService;
use App\Domains\Staff\Infrastructure\Models\Staff;
use App\Domains\Staff\Presentation\Filament\Resources\StaffResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditStaff extends EditRecord
{
    protected static string $resource = StaffResource::class;

    protected static ?string $title = 'Modifier un membre du personnel';

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        return app(StaffService::class)->update($this->staff(), $data, auth()->user());
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    private function staff(): Staff
    {
        return Staff::query()->findOrFail($this->record->getKey());
    }
}
