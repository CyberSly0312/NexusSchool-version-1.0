<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Presentation\Filament\Resources\GraduationResource\Pages;

use App\Domains\Enrollment\Presentation\Filament\Resources\GraduationResource;
use Filament\Resources\Pages\ListRecords;

class ListGraduations extends ListRecords
{
    protected static string $resource = GraduationResource::class;

    protected static ?string $title = 'Diplômes';
}
