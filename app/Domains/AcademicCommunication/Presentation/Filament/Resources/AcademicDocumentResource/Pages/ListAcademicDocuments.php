<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Presentation\Filament\Resources\AcademicDocumentResource\Pages;

use App\Domains\AcademicCommunication\Presentation\Filament\Resources\AcademicDocumentResource;
use Filament\Resources\Pages\ListRecords;

class ListAcademicDocuments extends ListRecords
{
    protected static string $resource = AcademicDocumentResource::class;

    protected static ?string $title = 'Documents académiques';
}
