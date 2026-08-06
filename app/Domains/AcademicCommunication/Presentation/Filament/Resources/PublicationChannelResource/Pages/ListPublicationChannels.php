<?php

declare(strict_types=1);

namespace App\Domains\AcademicCommunication\Presentation\Filament\Resources\PublicationChannelResource\Pages;

use App\Domains\AcademicCommunication\Presentation\Filament\Resources\PublicationChannelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPublicationChannels extends ListRecords
{
    protected static string $resource = PublicationChannelResource::class;

    protected static ?string $title = 'Canaux de publication';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
