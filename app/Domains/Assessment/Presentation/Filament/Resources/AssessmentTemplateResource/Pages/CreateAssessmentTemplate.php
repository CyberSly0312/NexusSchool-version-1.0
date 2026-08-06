<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Presentation\Filament\Resources\AssessmentTemplateResource\Pages;

use App\Domains\Assessment\Infrastructure\Models\AssessmentTemplate;
use App\Domains\Assessment\Presentation\Filament\Resources\AssessmentTemplateResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CreateAssessmentTemplate extends CreateRecord
{
    protected static string $resource = AssessmentTemplateResource::class;

    protected static ?string $title = 'Nouveau modèle d\'évaluation';

    protected function handleRecordCreation(array $data): Model
    {
        return AssessmentTemplate::create([
            ...$data,
            'school_id' => tenant()?->id,
            'uuid' => (string) Str::ulid(),
            'created_by' => auth()->id(),
        ]);
    }
}
