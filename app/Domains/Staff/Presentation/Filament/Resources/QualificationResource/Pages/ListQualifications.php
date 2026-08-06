<?php

declare(strict_types=1);

namespace App\Domains\Staff\Presentation\Filament\Resources\QualificationResource\Pages;

use App\Domains\Staff\Presentation\Filament\Resources\QualificationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListQualifications extends ListRecords
{
    protected static string $resource = QualificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
