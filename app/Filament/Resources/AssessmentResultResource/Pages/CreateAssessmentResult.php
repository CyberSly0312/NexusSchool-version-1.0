<?php

namespace App\Filament\Resources\AssessmentResultResource\Pages;

use App\Domains\Gradebook\DTOs\AssessmentResultData;
use App\Domains\Gradebook\Services\GradebookService;
use App\Filament\Resources\AssessmentResultResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAssessmentResult extends CreateRecord
{
    protected static string $resource = AssessmentResultResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $dto = AssessmentResultData::fromArray($data);

        return app(GradebookService::class)->record($dto);
    }
}
