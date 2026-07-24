<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $panelId = filament()->getCurrentPanel()->getId();

        if ($panelId === 'school-admin') {
            $data['school_id'] = tenant()?->id;
        }

        return $data;
    }
}
