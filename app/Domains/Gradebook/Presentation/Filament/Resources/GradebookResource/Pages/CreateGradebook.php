<?php

declare(strict_types=1);

namespace App\Domains\Gradebook\Presentation\Filament\Resources\GradebookResource\Pages;

use App\Domains\Gradebook\Application\DTOs\CreateGradebookData;
use App\Domains\Gradebook\Application\GradebookService;
use App\Domains\Gradebook\Presentation\Filament\Resources\GradebookResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateGradebook extends CreateRecord
{
    protected static string $resource = GradebookResource::class;

    protected static ?string $title = 'Nouveau carnet de notes';

    protected function handleRecordCreation(array $data): Model
    {
        $data['school_id'] = tenant()?->id;

        return app(GradebookService::class)->createGradebook(
            CreateGradebookData::fromArray($data),
            auth()->user(),
        );
    }
}
