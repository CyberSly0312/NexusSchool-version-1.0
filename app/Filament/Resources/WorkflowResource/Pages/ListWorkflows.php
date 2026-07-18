<?php

declare(strict_types=1);

namespace App\Filament\Resources\WorkflowResource\Pages;

use App\Filament\Resources\WorkflowResource;
use Filament\Actions;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListWorkflows extends ListRecords
{
    protected static string $resource = WorkflowResource::class;

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
            'active' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', true)),
            'draft' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'draft')),
            'archived' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'archived')),
        ];
    }
}
