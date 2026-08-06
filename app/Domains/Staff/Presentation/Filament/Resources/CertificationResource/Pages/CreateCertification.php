<?php

declare(strict_types=1);

namespace App\Domains\Staff\Presentation\Filament\Resources\CertificationResource\Pages;

use App\Domains\Staff\Presentation\Filament\Resources\CertificationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCertification extends CreateRecord
{
    protected static string $resource = CertificationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
