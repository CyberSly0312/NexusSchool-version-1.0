<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources\AcademicProgramResource\Pages;

use App\Domains\Academic\Presentation\Filament\Resources\AcademicProgramResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAcademicProgram extends EditRecord
{
    protected static string $resource = AcademicProgramResource::class;

    protected static ?string $title = 'Modifier un programme';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
