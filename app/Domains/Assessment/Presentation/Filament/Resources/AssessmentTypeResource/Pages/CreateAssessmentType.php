<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Presentation\Filament\Resources\AssessmentTypeResource\Pages;

use App\Domains\Assessment\Infrastructure\Models\AssessmentType;
use App\Domains\Assessment\Presentation\Filament\Resources\AssessmentTypeResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CreateAssessmentType extends CreateRecord
{
    protected static string $resource = AssessmentTypeResource::class;

    protected static ?string $title = 'Nouveau type d\'évaluation';

    protected function handleRecordCreation(array $data): Model
    {
        return AssessmentType::create([
            ...$data,
            'school_id' => tenant()?->id,
            'code' => $data['code'] ?? 'AT-'.Str::upper(Str::random(6)),
        ]);
    }
}
