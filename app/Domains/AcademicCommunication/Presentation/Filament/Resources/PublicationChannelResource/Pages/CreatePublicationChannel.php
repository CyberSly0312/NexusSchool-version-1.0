<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Presentation\Filament\Resources\PublicationChannelResource\Pages;

use App\Domains\AcademicCommunication\Presentation\Filament\Resources\PublicationChannelResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePublicationChannel extends CreateRecord
{
    protected static string $resource = PublicationChannelResource::class;

    protected static ?string $title = 'Nouveau canal de publication';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
