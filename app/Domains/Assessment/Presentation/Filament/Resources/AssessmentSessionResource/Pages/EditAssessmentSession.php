<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Presentation\Filament\Resources\AssessmentSessionResource\Pages;

use App\Domains\Assessment\Infrastructure\Models\AssessmentSession;
use App\Domains\Assessment\Presentation\Filament\Resources\AssessmentSessionResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditAssessmentSession extends EditRecord
{
    protected static string $resource = AssessmentSessionResource::class;

    protected static ?string $title = 'Modifier la session d\'évaluation';

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);

        return $record;
    }
}
