<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Presentation\Filament\Resources\CouncilSessionResource\Pages;

use App\Domains\ClassCouncil\Presentation\Filament\Resources\CouncilSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCouncilSessions extends ListRecords
{
    protected static string $resource = CouncilSessionResource::class;

    protected static ?string $title = 'Séances du conseil';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
