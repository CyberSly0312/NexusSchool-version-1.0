<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources\AcademicSubjectResource\Pages;

use App\Domains\Academic\Presentation\Filament\Resources\AcademicSubjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAcademicSubjects extends ListRecords
{
    protected static string $resource = AcademicSubjectResource::class;

    protected static ?string $title = 'Matières';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
