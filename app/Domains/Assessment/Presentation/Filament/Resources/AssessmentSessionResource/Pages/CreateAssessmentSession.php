<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Presentation\Filament\Resources\AssessmentSessionResource\Pages;

use App\Domains\Assessment\Infrastructure\Models\AssessmentSession;
use App\Domains\Assessment\Presentation\Filament\Resources\AssessmentSessionResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CreateAssessmentSession extends CreateRecord
{
    protected static string $resource = AssessmentSessionResource::class;

    protected static ?string $title = 'Nouvelle session d\'évaluation';

    protected function handleRecordCreation(array $data): Model
    {
        return AssessmentSession::create([
            ...$data,
            'school_id' => tenant()?->id,
            'uuid' => (string) Str::ulid(),
        ]);
    }
}
