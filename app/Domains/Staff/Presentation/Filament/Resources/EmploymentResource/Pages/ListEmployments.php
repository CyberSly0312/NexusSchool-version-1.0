<?php

declare(strict_types=1);

namespace App\Domains\Staff\Presentation\Filament\Resources\EmploymentResource\Pages;

use App\Domains\Staff\Presentation\Filament\Resources\EmploymentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEmployments extends ListRecords
{
    protected static string $resource = EmploymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
