<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources\AcademicSubjectResource\Pages;

use App\Domains\Academic\Presentation\Filament\Resources\AcademicSubjectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAcademicSubject extends EditRecord
{
    protected static string $resource = AcademicSubjectResource::class;

    protected static ?string $title = 'Modifier une matière';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
