<?php

declare(strict_types=1);

namespace App\Domains\Staff\Presentation\Filament\Resources\AssignmentResource\Pages;

use App\Domains\Staff\Presentation\Filament\Resources\AssignmentResource;
use Filament\Resources\Pages\EditRecord;

class EditAssignment extends EditRecord
{
    protected static string $resource = AssignmentResource::class;

    protected static ?string $title = 'Modifier une affectation';

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
