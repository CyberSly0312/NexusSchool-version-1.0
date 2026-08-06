<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Presentation\Filament\Resources\PublicationPolicyResource\Pages;

use App\Domains\AcademicCommunication\Presentation\Filament\Resources\PublicationPolicyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPublicationPolicies extends ListRecords
{
    protected static string $resource = PublicationPolicyResource::class;

    protected static ?string $title = 'Politiques de publication';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
