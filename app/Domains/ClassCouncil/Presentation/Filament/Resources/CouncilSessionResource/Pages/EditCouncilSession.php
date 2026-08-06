<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Presentation\Filament\Resources\CouncilSessionResource\Pages;

use App\Domains\ClassCouncil\Application\ClassCouncilService;
use App\Domains\ClassCouncil\Domain\Enums\CouncilSessionStatus;
use App\Domains\ClassCouncil\Presentation\Filament\Resources\CouncilSessionResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditCouncilSession extends EditRecord
{
    protected static string $resource = CouncilSessionResource::class;

    protected static ?string $title = 'Séance du conseil';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('convene')
                ->label('Réunir le conseil')
                ->icon('heroicon-o-bell')
                ->visible(fn (): bool => $this->record->status === CouncilSessionStatus::Draft)
                ->action(function (): void {
                    app(ClassCouncilService::class)->conveneSession($this->record, auth()->user());

                    Notification::make()->title('Conseil réuni')->success()->send();
                }),
            Actions\Action::make('publish')
                ->label('Publier')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (): bool => $this->record->status === CouncilSessionStatus::Convened)
                ->action(function (): void {
                    app(ClassCouncilService::class)->publishSession($this->record, auth()->user());

                    Notification::make()->title('Séance publiée')->success()->send();
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
