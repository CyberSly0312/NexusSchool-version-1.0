<?php

declare(strict_types=1);

namespace App\Domains\Staff\Presentation\Filament\Resources\EmploymentResource\Pages;

use App\Domains\Staff\Presentation\Filament\Resources\EmploymentResource;
use Filament\Resources\Pages\EditRecord;

class EditEmployment extends EditRecord
{
    protected static string $resource = EmploymentResource::class;

    protected static ?string $title = 'Modifier un contrat';

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
