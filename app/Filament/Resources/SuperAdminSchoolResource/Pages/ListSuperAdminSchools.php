<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuperAdminSchoolResource\Pages;

use App\Filament\Resources\SuperAdminSchoolResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSuperAdminSchools extends ListRecords
{
    protected static string $resource = SuperAdminSchoolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
