<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Presentation\Filament\Resources\AssessmentTemplateResource\Pages;

use App\Domains\Assessment\Infrastructure\Models\AssessmentTemplate;
use App\Domains\Assessment\Presentation\Filament\Resources\AssessmentTemplateResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditAssessmentTemplate extends EditRecord
{
    protected static string $resource = AssessmentTemplateResource::class;

    protected static ?string $title = 'Modifier le modèle d\'évaluation';

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        return $record;
    }
}
