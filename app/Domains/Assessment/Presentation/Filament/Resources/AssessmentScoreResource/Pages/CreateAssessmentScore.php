<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Presentation\Filament\Resources\AssessmentScoreResource\Pages;

use App\Domains\Assessment\Infrastructure\Models\AssessmentScore;
use App\Domains\Assessment\Presentation\Filament\Resources\AssessmentScoreResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CreateAssessmentScore extends CreateRecord
{
    protected static string $resource = AssessmentScoreResource::class;

    protected static ?string $title = 'Nouvelle note';

    protected function handleRecordCreation(array $data): Model
    {
        return AssessmentScore::create([
            ...$data,
            'school_id' => tenant()?->id,
            'uuid' => (string) Str::ulid(),
            'graded_at' => now(),
        ]);
    }
}
