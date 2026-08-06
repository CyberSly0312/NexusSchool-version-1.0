<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Presentation\Filament\Resources\AssessmentTypeResource\Pages;

use App\Domains\Assessment\Infrastructure\Models\AssessmentType;
use App\Domains\Assessment\Presentation\Filament\Resources\AssessmentTypeResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditAssessmentType extends EditRecord
{
    protected static string $resource = AssessmentTypeResource::class;

    protected static ?string $title = 'Modifier le type d\'évaluation';

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        return $record;
    }
}
