<?php

declare(strict_types=1);

namespace App\Domains\AcademicPolicy\Presentation\Filament\Resources\AcademicPolicyResource\Pages;

use App\Domains\AcademicPolicy\Presentation\Filament\Resources\AcademicPolicyResource;
use Filament\Resources\Pages\ListRecords;

class ListAcademicPolicies extends ListRecords
{
    protected static string $resource = AcademicPolicyResource::class;

    protected static ?string $title = 'Politiques académiques';
}
