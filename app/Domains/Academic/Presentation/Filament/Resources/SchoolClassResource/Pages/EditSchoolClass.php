<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources\SchoolClassResource\Pages;

use App\Domains\Academic\Presentation\Filament\Resources\SchoolClassResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSchoolClass extends EditRecord
{
    protected static string $resource = SchoolClassResource::class;

    protected static ?string $title = 'Modifier une classe';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
