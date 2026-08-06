<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Presentation\Filament\Resources\AssessmentScoreResource\Pages;

use App\Domains\Assessment\Infrastructure\Models\AssessmentScore;
use App\Domains\Assessment\Presentation\Filament\Resources\AssessmentScoreResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditAssessmentScore extends EditRecord
{
    protected static string $resource = AssessmentScoreResource::class;

    protected static ?string $title = 'Modifier la note';

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->assertMutable();

        $record->update($data);

        return $record;
    }
}
