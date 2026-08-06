<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Presentation\Filament\Resources\AssessmentScoreResource\Pages;

use App\Domains\Assessment\Infrastructure\Models\AssessmentScore;
use App\Domains\Assessment\Presentation\Filament\Resources\AssessmentScoreResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAssessmentScores extends ListRecords
{
    protected static string $resource = AssessmentScoreResource::class;

    protected static ?string $title = 'Notes d\'évaluation';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
