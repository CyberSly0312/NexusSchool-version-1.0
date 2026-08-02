<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources\EducationLevelResource\Pages;

use App\Domains\Academic\Presentation\Filament\Resources\EducationLevelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEducationLevel extends EditRecord
{
    protected static string $resource = EducationLevelResource::class;

    protected static ?string $title = 'Modifier un niveau';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
