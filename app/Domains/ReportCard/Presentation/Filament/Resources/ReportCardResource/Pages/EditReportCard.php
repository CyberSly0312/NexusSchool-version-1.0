<?php

declare(strict_types=1);

namespace App\Domains\ReportCard\Presentation\Filament\Resources\ReportCardResource\Pages;

use App\Domains\ReportCard\Application\ReportCardService;
use App\Domains\ReportCard\Domain\Enums\ReportCardStatus;
use App\Domains\ReportCard\Presentation\Filament\Resources\ReportCardResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditReportCard extends EditRecord
{
    protected static string $resource = ReportCardResource::class;

    protected static ?string $title = 'Bulletin';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('generate')
                ->label('Générer')
                ->icon('heroicon-o-play')
                ->visible(fn (): bool => $this->record->status !== ReportCardStatus::Generated && $this->record->status !== ReportCardStatus::Published)
                ->action(function (): void {
                    app(ReportCardService::class)->generateReportCard($this->record, auth()->user());

                    Notification::make()->title('Bulletins générés')->success()->send();
                }),
            Actions\Action::make('regenerate')
                ->label('Régénérer')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->visible(fn (): bool => $this->record->status->isRegenerable())
                ->action(function (): void {
                    app(ReportCardService::class)->regenerateReportCard($this->record, auth()->user());

                    Notification::make()->title('Bulletins régénérés')->success()->send();
                }),
            Actions\Action::make('publish')
                ->label('Publier')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (): bool => $this->record->status === ReportCardStatus::Generated)
                ->action(function (): void {
                    app(ReportCardService::class)->publishReportCard($this->record, auth()->user());

                    Notification::make()->title('Bulletins publiés')->success()->send();
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
