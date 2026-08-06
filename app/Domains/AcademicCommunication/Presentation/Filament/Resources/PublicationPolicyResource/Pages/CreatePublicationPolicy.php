<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Presentation\Filament\Resources\PublicationPolicyResource\Pages;

use App\Domains\AcademicCommunication\Presentation\Filament\Resources\PublicationPolicyResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePublicationPolicy extends CreateRecord
{
    protected static string $resource = PublicationPolicyResource::class;

    protected static ?string $title = 'Nouvelle politique de publication';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
