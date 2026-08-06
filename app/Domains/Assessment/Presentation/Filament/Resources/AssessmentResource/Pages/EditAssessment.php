<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Presentation\Filament\Resources\AssessmentResource\Pages;

use App\Domains\Assessment\Application\DTOs\UpdateAssessmentData;
use App\Domains\Assessment\Application\Services\AssessmentService;
use App\Domains\Assessment\Presentation\Filament\Resources\AssessmentResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditAssessment extends EditRecord
{
    protected static string $resource = AssessmentResource::class;

    protected static ?string $title = 'Modifier l\'évaluation';

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $data['assessment_id'] = $record->getKey();
        $data['school_id'] = tenant()?->id;

        app(AssessmentService::class)->updateAssessment(
            $record,
            UpdateAssessmentData::fromArray($data),
            auth()->user(),
        );

        return $record;
    }
}
