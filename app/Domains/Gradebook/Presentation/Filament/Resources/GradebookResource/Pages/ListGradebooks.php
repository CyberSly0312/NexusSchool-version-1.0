<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Presentation\Filament\Resources\GradebookResource\Pages;

use App\Domains\Gradebook\Presentation\Filament\Resources\GradebookResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGradebooks extends ListRecords
{
    protected static string $resource = GradebookResource::class;

    protected static ?string $title = 'Carnets de notes';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
