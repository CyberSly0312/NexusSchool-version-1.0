<?php

declare(strict_types=1);

namespace App\Domains\Academic\Presentation\Filament\Resources\SubjectGroupResource\Pages;

use App\Domains\Academic\Presentation\Filament\Resources\SubjectGroupResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSubjectGroup extends CreateRecord
{
    protected static string $resource = SubjectGroupResource::class;

    protected static ?string $title = 'Créer un groupe de matières';
}
