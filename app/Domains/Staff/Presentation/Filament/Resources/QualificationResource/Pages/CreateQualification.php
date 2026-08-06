<?php

declare(strict_types=1);

namespace App\Domains\Staff\Presentation\Filament\Resources\QualificationResource\Pages;

use App\Domains\Staff\Presentation\Filament\Resources\QualificationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateQualification extends CreateRecord
{
    protected static string $resource = QualificationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
