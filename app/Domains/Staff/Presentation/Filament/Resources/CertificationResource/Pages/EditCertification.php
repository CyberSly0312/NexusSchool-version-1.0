<?php

declare(strict_types=1);

namespace App\Domains\Staff\Presentation\Filament\Resources\CertificationResource\Pages;

use App\Domains\Staff\Presentation\Filament\Resources\CertificationResource;
use Filament\Resources\Pages\EditRecord;

class EditCertification extends EditRecord
{
    protected static string $resource = CertificationResource::class;

    protected static ?string $title = 'Modifier une certification';

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
