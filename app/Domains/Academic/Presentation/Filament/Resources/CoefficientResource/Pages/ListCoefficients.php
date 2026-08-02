<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources\CoefficientResource\Pages;

use App\Domains\Academic\Presentation\Filament\Resources\CoefficientResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCoefficients extends ListRecords
{
    protected static string $resource = CoefficientResource::class;

    protected static ?string $title = 'Coefficients';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
