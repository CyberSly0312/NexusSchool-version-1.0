<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Presentation\Filament\Resources\GradeEntryResource\Pages;

use App\Domains\Gradebook\Presentation\Filament\Resources\GradeEntryResource;
use Filament\Resources\Pages\ListRecords;

class ListGradeEntries extends ListRecords
{
    protected static string $resource = GradeEntryResource::class;

    protected static ?string $title = 'Notes par élève';
}
