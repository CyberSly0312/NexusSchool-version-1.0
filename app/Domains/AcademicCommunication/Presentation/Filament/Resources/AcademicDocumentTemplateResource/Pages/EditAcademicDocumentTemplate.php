<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Presentation\Filament\Resources\AcademicDocumentTemplateResource\Pages;

use App\Domains\AcademicCommunication\Presentation\Filament\Resources\AcademicDocumentTemplateResource;
use Filament\Resources\Pages\EditRecord;

class EditAcademicDocumentTemplate extends EditRecord
{
    protected static string $resource = AcademicDocumentTemplateResource::class;

    protected static ?string $title = 'Modifier le modèle de document';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
