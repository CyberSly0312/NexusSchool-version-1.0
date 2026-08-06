<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Presentation\Filament\Resources\AssessmentTemplateResource\Pages;

use App\Domains\Assessment\Presentation\Filament\Resources\AssessmentTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAssessmentTemplates extends ListRecords
{
    protected static string $resource = AssessmentTemplateResource::class;

    protected static ?string $title = 'Modèles d\'évaluation';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
