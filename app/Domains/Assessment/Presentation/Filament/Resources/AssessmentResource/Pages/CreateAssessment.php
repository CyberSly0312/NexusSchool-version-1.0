<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Presentation\Filament\Resources\AssessmentResource\Pages;

use App\Domains\Assessment\Application\DTOs\CreateAssessmentData;
use App\Domains\Assessment\Application\Services\AssessmentService;
use App\Domains\Assessment\Presentation\Filament\Resources\AssessmentResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateAssessment extends CreateRecord
{
    protected static string $resource = AssessmentResource::class;

    protected static ?string $title = 'Nouvelle évaluation';

    protected function handleRecordCreation(array $data): Model
    {
        $data['school_id'] = tenant()?->id;

        return app(AssessmentService::class)->createAssessment(
            CreateAssessmentData::fromArray($data),
            auth()->user(),
        );
    }
}
