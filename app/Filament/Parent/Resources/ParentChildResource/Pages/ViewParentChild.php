<?php

declare(strict_types=1);

namespace App\Filament\Parent\Resources\ParentChildResource\Pages;

use App\Domains\Guardian\Application\Services\GuardianChildAccessService;
use App\Domains\Guardian\Domain\Enums\GuardianChildOperation;
use App\Domains\Guardian\Domain\Exceptions\GuardianChildAccessDeniedException;
use App\Filament\Parent\Resources\ParentChildResource;
use Filament\Resources\Pages\ViewRecord;

final class ViewParentChild extends ViewRecord
{
    protected static string $resource = ParentChildResource::class;

    protected static ?string $title = 'Fiche de mon enfant';

    public function mount(int|string $record): void
    {
        try {
            $relationship = app(GuardianChildAccessService::class)->assertCanAccess(
                $record,
                GuardianChildOperation::View,
            );

            parent::mount($record);

            app(GuardianChildAccessService::class)->recordChildViewed($relationship);
        } catch (GuardianChildAccessDeniedException) {
            abort(403);
        }
    }

    /**
     * @return array<int, mixed>
     */
    protected function getHeaderActions(): array
    {
        return [];
    }
}
