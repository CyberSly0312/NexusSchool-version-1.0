<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Presentation\Filament\Resources\PublicationChannelResource\Pages;

use App\Domains\AcademicCommunication\Presentation\Filament\Resources\PublicationChannelResource;
use Filament\Resources\Pages\EditRecord;

class EditPublicationChannel extends EditRecord
{
    protected static string $resource = PublicationChannelResource::class;

    protected static ?string $title = 'Modifier le canal de publication';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
