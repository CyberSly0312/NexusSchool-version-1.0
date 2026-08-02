<?php

declare(strict_types=1);

namespace App\Domains\Student\Presentation\Filament\Resources\StudentResource\Pages;

use App\Domains\Student\Presentation\Filament\Resources\StudentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected static ?string $title = 'Élèves';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Nouvel élève'),
        ];
    }
}
