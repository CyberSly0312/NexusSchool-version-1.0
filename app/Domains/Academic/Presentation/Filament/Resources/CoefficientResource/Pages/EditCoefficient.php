<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources\CoefficientResource\Pages;

use App\Domains\Academic\Presentation\Filament\Resources\CoefficientResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCoefficient extends EditRecord
{
    protected static string $resource = CoefficientResource::class;

    protected static ?string $title = 'Modifier un coefficient';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
