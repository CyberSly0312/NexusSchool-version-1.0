<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources\AcademicProgramResource\Pages;

use App\Domains\Academic\Presentation\Filament\Resources\AcademicProgramResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAcademicPrograms extends ListRecords
{
    protected static string $resource = AcademicProgramResource::class;

    protected static ?string $title = 'Programmes';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
