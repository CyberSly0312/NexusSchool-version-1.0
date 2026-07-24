<?php

declare(strict_types=1);

namespace App\Filament\Resources\SuperAdminSchoolResource\Pages;

use App\Filament\Resources\SuperAdminSchoolResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditSuperAdminSchool extends EditRecord
{
    protected static string $resource = SuperAdminSchoolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
