<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources\SubjectGroupResource\Pages;

use App\Domains\Academic\Presentation\Filament\Resources\SubjectGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubjectGroup extends EditRecord
{
    protected static string $resource = SubjectGroupResource::class;

    protected static ?string $title = 'Modifier un groupe de matières';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
