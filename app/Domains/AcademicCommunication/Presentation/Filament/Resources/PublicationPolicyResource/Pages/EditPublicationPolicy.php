<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Presentation\Filament\Resources\PublicationPolicyResource\Pages;

use App\Domains\AcademicCommunication\Presentation\Filament\Resources\PublicationPolicyResource;
use Filament\Resources\Pages\EditRecord;

class EditPublicationPolicy extends EditRecord
{
    protected static string $resource = PublicationPolicyResource::class;

    protected static ?string $title = 'Modifier la politique de publication';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
