<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Presentation\Filament\Resources\EnrollmentHistoryResource\Pages;

use App\Domains\Enrollment\Presentation\Filament\Resources\EnrollmentHistoryResource;
use Filament\Resources\Pages\ListRecords;

class ListEnrollmentHistory extends ListRecords
{
    protected static string $resource = EnrollmentHistoryResource::class;

    protected static ?string $title = 'Historique des inscriptions';
}
