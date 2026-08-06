<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Presentation\Filament\Resources\AssessmentSessionResource\Pages;

use App\Domains\Assessment\Infrastructure\Models\AssessmentSession;
use App\Domains\Assessment\Presentation\Filament\Resources\AssessmentSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAssessmentSessions extends ListRecords
{
    protected static string $resource = AssessmentSessionResource::class;

    protected static ?string $title = 'Sessions d\'évaluation';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
