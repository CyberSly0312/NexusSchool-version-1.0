<?php

declare(strict_types=1);

namespace App\Domains\ClassCouncil\Presentation\Filament\Resources\CouncilSessionResource\Pages;

use App\Domains\ClassCouncil\Application\ClassCouncilService;
use App\Domains\ClassCouncil\Application\DTOs\CreateCouncilSessionData;
use App\Domains\ClassCouncil\Presentation\Filament\Resources\CouncilSessionResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateCouncilSession extends CreateRecord
{
    protected static string $resource = CouncilSessionResource::class;

    protected static ?string $title = 'Nouvelle séance du conseil';

    protected function handleRecordCreation(array $data): Model
    {
        $data['school_id'] = tenant()?->id;

        return app(ClassCouncilService::class)->createSession(
            CreateCouncilSessionData::fromArray($data),
            auth()->user(),
        );
    }
}
