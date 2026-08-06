<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Presentation\Filament\Resources\AcademicDocumentTemplateResource\Pages;

use App\Domains\AcademicCommunication\Presentation\Filament\Resources\AcademicDocumentTemplateResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAcademicDocumentTemplate extends CreateRecord
{
    protected static string $resource = AcademicDocumentTemplateResource::class;

    protected static ?string $title = 'Nouveau modèle de document';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
