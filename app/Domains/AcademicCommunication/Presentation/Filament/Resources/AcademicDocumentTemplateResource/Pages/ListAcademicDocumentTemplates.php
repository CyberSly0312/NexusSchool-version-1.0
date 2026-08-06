<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Presentation\Filament\Resources\AcademicDocumentTemplateResource\Pages;

use App\Domains\AcademicCommunication\Presentation\Filament\Resources\AcademicDocumentTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAcademicDocumentTemplates extends ListRecords
{
    protected static string $resource = AcademicDocumentTemplateResource::class;

    protected static ?string $title = 'Modèles de documents';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
