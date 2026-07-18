<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected static ?string $title = 'Users';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(),
            'active' => Tab::make()->modifyQueryUsing(fn ($query) => $query->where('status', 'active')),
            'inactive' => Tab::make()->modifyQueryUsing(fn ($query) => $query->where('status', 'inactive')),
            'pending' => Tab::make()->modifyQueryUsing(fn ($query) => $query->where('status', 'pending')),
            'suspended' => Tab::make()->modifyQueryUsing(fn ($query) => $query->where('status', 'suspended')),
        ];
    }
}
