<?php

declare(strict_types=1);

namespace App\Domains\Assessment\Presentation\Filament\Resources\AssessmentStatisticsResource\Pages;

use App\Domains\Assessment\Application\AssessmentService;
use App\Domains\Assessment\Infrastructure\Models\Assessment;
use App\Domains\Assessment\Presentation\Filament\Resources\AssessmentStatisticsResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListAssessmentStatistics extends ListRecords
{
    protected static string $resource = AssessmentStatisticsResource::class;

    protected static ?string $title = 'Statistiques d\'évaluation';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('generate')
                ->label('Générer les statistiques')
                ->icon('heroicon-o-chart-bar')
                ->requiresConfirmation()
                ->action(function (): void {
                    $service = app(AssessmentService::class);

                    $count = Assessment::query()
                        ->forSchool(tenant()?->id ?? '')
                        ->whereIn('status', ['published', 'completed'])
                        ->get()
                        ->each(fn (Assessment $assessment) => $service->generateStatistics($assessment))
                        ->count();

                    Notification::make()
                        ->title('Statistiques générées')
                        ->body("{$count} évaluation(s) traitée(s).")
                        ->success()
                        ->send();
                }),
        ];
    }
}
