<?php

declare(strict_types=1);

namespace App\Domains\Guardian\Presentation\Filament\Resources\EmergencyContactsResource\Pages;

use App\Domains\Guardian\Presentation\Filament\Resources\EmergencyContactsResource;
use Filament\Resources\Pages\ListRecords;

class ListEmergencyContacts extends ListRecords
{
    protected static string $resource = EmergencyContactsResource::class;
}
