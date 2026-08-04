<?php

declare(strict_types=1);

namespace App\Domains\Enrollment\Presentation\Filament\Resources\TransferResource\Pages;

use App\Domains\Enrollment\Presentation\Filament\Resources\TransferResource;
use Filament\Resources\Pages\ListRecords;

class ListTransfers extends ListRecords
{
    protected static string $resource = TransferResource::class;

    protected static ?string $title = 'Transferts';
}
