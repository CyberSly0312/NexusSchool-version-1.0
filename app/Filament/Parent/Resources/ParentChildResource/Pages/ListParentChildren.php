<?php

declare(strict_types=1);

namespace App\Filament\Parent\Resources\ParentChildResource\Pages;

use App\Domains\Guardian\Application\Services\GuardianChildAccessService;
use App\Domains\Guardian\Domain\Exceptions\GuardianChildAccessDeniedException;
use App\Filament\Parent\Resources\ParentChildResource;
use Filament\Resources\Pages\ListRecords;

final class ListParentChildren extends ListRecords
{
    protected static string $resource = ParentChildResource::class;

    protected static ?string $title = 'Mes enfants';

    public function mount(): void
    {
        try {
            app(GuardianChildAccessService::class)->assertParentContext();
        } catch (GuardianChildAccessDeniedException) {
            abort(403);
        }

        parent::mount();
    }

    /**
     * @return array<int, mixed>
     */
    protected function getHeaderActions(): array
    {
        return [];
    }
}
