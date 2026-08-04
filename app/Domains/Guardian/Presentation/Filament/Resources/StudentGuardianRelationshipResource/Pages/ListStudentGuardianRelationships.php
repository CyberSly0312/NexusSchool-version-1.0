<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Presentation\Filament\Resources\StudentGuardianRelationshipResource\Pages;

use App\Domains\Guardian\Presentation\Filament\Resources\StudentGuardianRelationshipResource;
use Filament\Resources\Pages\ListRecords;

class ListStudentGuardianRelationships extends ListRecords
{
    protected static string $resource = StudentGuardianRelationshipResource::class;
}
