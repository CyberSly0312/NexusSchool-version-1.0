<?php

declare(strict_types=1);

namespace App\Domains\Staff\Presentation\Filament\Resources\QualificationResource\Pages;

use App\Domains\Staff\Presentation\Filament\Resources\QualificationResource;
use Filament\Resources\Pages\EditRecord;

class EditQualification extends EditRecord
{
    protected static string $resource = QualificationResource::class;

    protected static ?string $title = 'Modifier une qualification';

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
