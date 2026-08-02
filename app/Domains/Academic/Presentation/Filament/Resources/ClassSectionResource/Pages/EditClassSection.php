<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources\ClassSectionResource\Pages;

use App\Domains\Academic\Presentation\Filament\Resources\ClassSectionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClassSection extends EditRecord
{
    protected static string $resource = ClassSectionResource::class;

    protected static ?string $title = 'Modifier une section';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
